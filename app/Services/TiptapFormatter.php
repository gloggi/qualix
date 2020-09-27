<?php

namespace App\Services;

use App\Exceptions\ParticipantObservationNotFoundException;
use App\Exceptions\RequirementNotFoundException;
use App\Exceptions\RequirementsOutdatedException;
use App\Models\ParticipantObservation;
use App\Models\Quali;
use App\Models\QualiContentNode;
use App\Models\Requirement;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TiptapFormatter {

    /** @var Quali */
    protected $quali;

    /** @var Collection */
    protected $allContents;

    public function __construct(Quali $quali) {
        $this->quali = $quali;
    }

    /**
     * Converts the quali contents from the quali model to the format used by the tiptap editor.
     *
     * @return array quali contents in tiptap format
     */
    public function toTiptap() {
        return self::wrapInDocument(self::contentsToTiptap($this->getAllQualiContents()->sortBy->get('order')));
    }

    /**
     * Converts the quali contents from the quali model to the format used by the tiptap editor.
     *
     * @param Collection $contents
     * @return Collection quali contents in tiptap format
     */
    protected static function contentsToTiptap(Collection $contents) {
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
     * Returns the highest order value stored in the database in any content of the quali.
     *
     * @return array quali contents in tiptap format
     */
    public function getHighestOrderNumber() {
        return $this->getAllQualiContents()->max->get('order');
    }

    /**
     * Updates the quali model with the contents from a tiptap editor.
     *
     * @param array $tiptap editor description of new quali contents
     * @throws RequirementsOutdatedException
     */
    public function applyToQuali(array $tiptap) {
        $contents = $this->tiptapToContents($tiptap);
        $this->checkRequirementsAreUpToDate($contents);

        [$requirements, $participantObservations, $contentNodes] = $this->contentsToModels($contents);
        $requirements = $requirements->mapWithKeys(function(Requirement $requirement) {
            return [$requirement->id => ['passed' => $requirement->pivot->passed, 'order' => $requirement->pivot->order]];
        });

        $this->quali->requirements()->sync($requirements);
        $this->quali->participant_observations()->detach();
        $participantObservations->each(function(ParticipantObservation $participantObservation) {
            $this->quali->participant_observations()->attach($participantObservation->id, ['order' => $participantObservation->order]);
        });
        $this->quali->contentNodes()->delete();
        $this->quali->contentNodes()->createMany($contentNodes);
    }

    public function contentsToModels(Collection $contents) {
        $order = 0;
        $requirements = [];
        $participantObservations = [];
        $contentNodes = [];
        $contents->each(function($node) use(&$order, &$requirements, &$participantObservations, &$contentNodes) {
            switch($node['type']) {
                case 'requirement':
                    /** @var Requirement|null $requirement */
                    $requirement = $this->quali->requirements()->find(data_get($node, 'attrs.id'));
                    if (!$requirement) throw new RequirementNotFoundException();
                    $requirement->pivot->order = $order;
                    $requirement->pivot->passed = data_get($node, 'attrs.passed', null);
                    $requirements[] = $requirement;
                    break;
                case 'observation':
                    $participantObservation = $this->quali->participant->participant_observations()->find(data_get($node, 'attrs.id'));
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
        return [collect($requirements), collect($participantObservations), collect($contentNodes)];
    }

    protected function modelsToContents(Collection $models) {
        return collect(array_filter($models->map(function ($model) {
            if ($model instanceof Requirement) {
                return collect([
                    'order' => $model->pivot->order,
                    'type' => 'requirement',
                    'attrs' => [
                        'id' => $model->id,
                        'passed' => $model->pivot->passed,
                    ]
                ]);
            }
            if ($model instanceof ParticipantObservation) {
                return collect([
                    'order' => $model->pivot->order,
                    'type' => 'observation',
                    'attrs' => [
                        'id' => $model->id,
                    ]
                ]);
            }
            if ($model instanceof QualiContentNode) {
                return collect(json_decode($model->json))
                    ->merge([
                        'order' => $model->order
                    ]);
            }
            return null;
        })->all()));
    }

    public static function createContentNodeJSON($paragraphText) {
        $content = $paragraphText ? ['content' =>  [[ 'type' => 'text', 'text' => $paragraphText ]]] : [];
        return json_encode(array_merge(['type' => 'paragraph'], $content));
    }

    protected function getAllQualiContents() {
        if (!$this->allContents) {
            $this->allContents = $this->modelsToContents(collect(array_merge(
                $this->quali->requirements->all(),
                $this->quali->participant_observations->all(),
                $this->quali->contentNodes->all()
            )));
        }
        return $this->allContents;
    }

    /**
     * Checks whether the requirements in the given tiptap formatted contents are the same set of requirements that are
     * assigned to the quali. Throws a RequirementsOutdatedException containing corrected tiptap formatted content if
     * there is a mismatch.
     *
     * @param Collection $contents
     * @throws RequirementsOutdatedException
     */
    protected function checkRequirementsAreUpToDate(Collection $contents) {
        $tiptapRequirementIds = $contents
            ->filter(function($node) { return data_get($node, 'type') === 'requirement'; })
            ->pluck('attrs.id');

        $qualiRequirements = $this->quali->requirements;
        $qualiRequirementIds = $qualiRequirements->pluck('id');

        if ($tiptapRequirementIds->sort()->values()->all() !== $qualiRequirementIds->sort()->values()->all()) {
            $stillValid = $contents->filter(function($node) use ($qualiRequirementIds) {
                return (data_get($node, 'type') !== 'requirement') || $qualiRequirementIds->containsStrict(data_get($node, 'attrs.id'));
            })->values();
            $missingRequirements = $this->modelsToContents($this->quali->requirements()->whereNotIn('requirements.id', $tiptapRequirementIds)->get());
            $correctedContents = $stillValid->merge($missingRequirements->all());

            throw new RequirementsOutdatedException(collect(self::wrapInDocument($correctedContents))->toJson());
        }
    }
}
