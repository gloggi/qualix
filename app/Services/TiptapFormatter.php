<?php

namespace App\Services;

use App\Exceptions\ParticipantObservationNotFoundException;
use App\Exceptions\RequirementNotFoundException;
use App\Exceptions\RequirementsMismatchException;
use App\Models\Feedback;
use App\Models\FeedbackContentNode;
use App\Models\FeedbackRequirement;
use App\Models\ParticipantObservation;
use App\Models\Requirement;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TiptapFormatter {

    /** @var Feedback */
    protected $feedback;

    /** @var Collection */
    protected $allContents;

    public function __construct(Feedback $feedback) {
        $this->feedback = $feedback;
    }

    /**
     * Converts the feedback contents from the feedback model to the format used by the tiptap editor.
     *
     * @return array feedback contents in tiptap format
     */
    public function toTiptap() {
        return self::wrapInDocument(self::removeOrderField($this->getAllFeedbackContents()->sortBy->get('order')));
    }

    /**
     * Converts the feedback contents from the feedback model to the format used by the tiptap editor.
     *
     * @param Collection $contents
     * @return Collection feedback contents in tiptap format
     */
    protected static function removeOrderField(Collection $contents) {
        return $contents->map(function($entry) { return Arr::except($entry, 'order'); })->values();
    }

    protected static function wrapInDocument(Collection $contents) {
        return [
            'type' => 'doc',
            'content' => $contents->all(),
        ];
    }

    /**
     * @param array $tiptap
     * @return Collection
     */
    protected static function tiptapToContents(array $tiptap) {
        return collect(data_get($tiptap, 'content', []));
    }

    /**
     * Updates the feedback model with the contents from a tiptap editor.
     *
     * @param array $tiptap editor description of new feedback contents
     * @param bool $checkRequirements whether to check that the requirements in the contents match the requirements in the feedback
     * @throws RequirementsMismatchException
     */
    public function applyToFeedback(array $tiptap, $checkRequirements = true) {
        $contents = $this->tiptapToContents($tiptap);
        if ($checkRequirements) $this->checkRequirementsAreUpToDate($contents);

        [$requirements, $participantObservations, $contentNodes] = $this->contentsToModels($contents, $checkRequirements);
        $feedbackRequirements = $requirements->map(function(FeedbackRequirement $requirement) {
            return [
                'requirement' => $requirement->requirement_id,
                'requirement_status' => $requirement->requirement_status_id,
                'order' => $requirement->order,
                'comment' => $requirement->comment ?? '',
            ];
        });

        $this->feedback->feedback_requirements()->delete();
        $this->feedback->feedback_requirements()->createMany($feedbackRequirements);
        $this->feedback->participant_observations()->detach();
        $participantObservations->each(function(ParticipantObservation $participantObservation) {
            $this->feedback->participant_observations()->attach($participantObservation->id, ['order' => $participantObservation->order]);
        });
        $this->feedback->contentNodes()->delete();
        $this->feedback->contentNodes()->createMany($contentNodes);

        // clear outdated caches on the feedback and on the formatter
        $this->feedback->unsetRelation('requirements');
        $this->feedback->unsetRelation('participant_observations');
        $this->feedback->unsetRelation('contentNodes');
        $this->allContents = null;
    }

    /**
     * @param Collection $contents
     * @param bool $onlyRequirementsFromFeedback
     * @return array
     */
    protected function contentsToModels(Collection $contents, $onlyRequirementsFromFeedback = true) {
        $order = 0;
        $feedbackRequirements = [];
        $participantObservations = [];
        $contentNodes = [];
        $contents->each(function($node) use($onlyRequirementsFromFeedback, &$order, &$feedbackRequirements, &$participantObservations, &$contentNodes) {
            switch($node['type']) {
                case 'requirement':
                    $allRequirements = $onlyRequirementsFromFeedback ? $this->feedback->requirements() : $this->feedback->feedback_data->course->requirements;
                    /** @var Requirement|null $requirement */
                    $requirement = $allRequirements->find(data_get($node, 'attrs.id'));
                    if (!$requirement) throw new RequirementNotFoundException();
                    $feedbackRequirement = new FeedbackRequirement();
                    $feedbackRequirement->requirement_id = $requirement->id;
                    $feedbackRequirement->order = $order;
                    $feedbackRequirement->requirement_status_id = data_get($node, 'attrs.status_id');
                    $feedbackRequirement->comment = data_get($node, 'attrs.comment');
                    $feedbackRequirements[] = $feedbackRequirement;
                    break;
                case 'observation':
                    $participantObservation = $this->feedback->participant->participant_observations()->find(data_get($node, 'attrs.id'));
                    if (!$participantObservation) throw new ParticipantObservationNotFoundException();
                    $participantObservation->order = $order;
                    $participantObservations[] = $participantObservation;
                    break;
                default:
                    $contentNodes[] = [ 'json' => json_encode($node), 'order' => $order ];
                    break;
            }
            $order++;
        });
        return [collect($feedbackRequirements), collect($participantObservations), collect($contentNodes)];
    }

    /**
     * @param Collection $models
     * @return Collection
     */
    protected static function modelsToContents(Collection $models, int $defaultRequirementStatusId) {
        return $models->map(function ($model) use($defaultRequirementStatusId) {
            if ($model instanceof Requirement) {
                return collect([
                    'order' => $model->order,
                    'type' => 'requirement',
                    'attrs' => [
                        'id' => $model->id,
                        'status_id' => $model->status_id ?? $defaultRequirementStatusId,
                        'comment' => $model->comment ?? '',
                    ],
                ]);
            }
            if ($model instanceof ParticipantObservation) {
                $pivot = $model->pivot ?? (object) ['order' => 0];
                return collect([
                    'order' => $pivot->order,
                    'type' => 'observation',
                    'attrs' => [
                        'id' => $model->id,
                    ],
                ]);
            }
            if ($model instanceof FeedbackContentNode) {
                return collect(json_decode($model->json))
                    ->merge([
                        'order' => $model->order,
                    ]);
            }
            return null;
        })->filter();
    }

    /**
     * Wraps the given string in a tiptap formatted paragraph.
     *
     * @param string $paragraphText
     * @return string
     */
    protected static function createContentNodeJSON($paragraphText) {
        $content = $paragraphText ? ['content' =>  [[ 'type' => 'text', 'text' => $paragraphText ]]] : [];
        return json_encode(array_merge(['type' => 'paragraph'], $content));
    }

    /**
     * @return Collection
     */
    protected function getAllFeedbackContents() {
        if (!$this->allContents) {
            $this->allContents = $this->modelsToContents(collect(array_merge(
                $this->feedback->requirements->all(),
                $this->feedback->participant_observations->all(),
                $this->feedback->contentNodes->all()
            )), $this->getDefaultRequirementStatusId());
        }
        return $this->allContents;
    }

    /**
     * Checks whether the requirements in the given tiptap formatted contents are the same set of requirements that are
     * assigned to the feedback. Throws a RequirementsOutdatedException containing corrected tiptap formatted content if
     * there is a mismatch.
     *
     * @param Collection $contents
     * @throws RequirementsMismatchException
     */
    protected function checkRequirementsAreUpToDate(Collection $contents) {
        $tiptapRequirementIds = $contents
            ->filter(function($node) { return data_get($node, 'type') === 'requirement'; })
            ->pluck('attrs.id');

        /** @var Collection $feedbackRequirements */
        $feedbackRequirements = $this->feedback->requirements;
        $feedbackRequirementIds = $feedbackRequirements->pluck('id');

        if ($tiptapRequirementIds->sort()->values()->all() !== $feedbackRequirementIds->sort()->values()->all()) {
            $stillValid = $contents->filter(function($node) use ($feedbackRequirementIds) {
                return (data_get($node, 'type') !== 'requirement') || $feedbackRequirementIds->containsStrict(data_get($node, 'attrs.id'));
            })->values();

            $missingRequirements = $this->feedback->requirements()->whereNotIn('requirements.id', $tiptapRequirementIds)->get();
            $correctedContents = self::appendRequirements($stillValid, $missingRequirements, $this->getDefaultRequirementStatusId());
            throw new RequirementsMismatchException(collect(self::wrapInDocument($correctedContents))->toJson());
        }
    }

    /**
     * Appends the given set of requirements to the feedback, separated by empty paragraphs.
     *
     * @param Collection $requirements
     * @throws RequirementsMismatchException
     */
    public function appendRequirementsToFeedback(Collection $requirements) {
        $this->applyToFeedback(self::wrapInDocument(
            self::appendRequirements(self::tiptapToContents($this->toTiptap()), $requirements, $this->getDefaultRequirementStatusId())
        ), false);
    }

    /**
     * Appends the given set of requirements to the given contents, separated by empty paragraphs.
     *
     * @param Collection $contents
     * @param Collection $requirements
     * @param int $defaultRequirementStatusId
     * @return Collection
     */
    protected static function appendRequirements(Collection $contents, Collection $requirements, int $defaultRequirementStatusId) {
        return $contents->merge($requirements->flatMap(function($requirement) use($defaultRequirementStatusId) {
            if (!($requirement instanceof Requirement)) return collect([]);
            return self::removeOrderField(self::modelsToContents(collect([
                $requirement,
                new FeedbackContentNode([ 'json' => self::createContentNodeJSON('') ]),
            ]), $defaultRequirementStatusId));
        }));
    }

    /**
     * Checks whether the given tiptap formatted content is valid and only contains known requirements and observations.
     *
     * @param array $contents
     * @param Collection $requirements
     * @param Collection $observations
     * @return bool
     */
    public static function isValid($contents, Collection $requirements, Collection $observations, Collection $validRequirementStatusIds) {
        if (!is_array($contents)) return false;
        if (!Arr::has($contents, 'type')) return false;
        if ($contents['type'] !== 'doc') return false;
        if (!Arr::has($contents, 'content')) return false;
        if (!is_array($contents['content'])) return false;
        foreach($contents['content'] as $node) {
            if (!is_array($node)) return false;
            if (!Arr::has($node, 'type')) return false;
            if (!is_string($node['type'])) return false;
            switch($node['type']) {
                case 'observation':
                    if (!Arr::has($node, 'attrs')) return false;
                    if (!is_array($node['attrs'])) return false;
                    if (!Arr::has($node,'attrs.id')) return false;
                    if (!$observations->contains($node['attrs']['id'])) return false;
                    break;
                case 'requirement':
                    if (!Arr::has($node, 'attrs')) return false;
                    if (!is_array($node['attrs'])) return false;
                    if (!Arr::has($node, 'attrs.id')) return false;
                    if (!$requirements->contains($node['attrs']['id'])) return false;
                    if (!Arr::has($node, 'attrs.status_id')) return false;
                    if (!$validRequirementStatusIds->contains($node['attrs']['status_id'])) return false;
                    if (!Arr::has($node, 'attrs.comment')) return false;
                    if (!is_string($node['attrs']['comment'])) return false;
                    break;
                default:
                    if (json_encode($node) === false) return false;
                    break;
            }
        }
        return true;
    }

    /**
     * @return int|null
     */
    protected function getDefaultRequirementStatusId() {
        return $this->feedback->feedback_data->course->default_requirement_status_id;
    }
}
