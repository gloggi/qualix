<?php

namespace App\Services;

use App\Exceptions\ObservationNotFoundException;
use App\Exceptions\RequirementNotFoundException;
use App\Exceptions\RequirementsOutdatedException;
use App\Models\Observation;
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
        $contents = $this->getAllContents()
            ->sortBy->order
            ->map(function($entry) { return Arr::except($entry, 'order'); })
            ->values();
        return [
            'type' => 'doc',
            'content' => $contents->all(),
        ];
    }

    /**
     * Returns the highest order value stored in the database in any content of the quali.
     *
     * @return array quali contents in tiptap format
     */
    public function getHighestOrderNumber() {
        return $this->getAllContents()->max->order;
    }

    /**
     * Updates the quali model with the contents from a tiptap editor.
     *
     * @param array $tiptap editor description of new quali contents
     * @throws RequirementsOutdatedException
     */
    public function applyToQuali(array $tiptap) {
        $this->checkRequirementsAreUpToDate($tiptap);

        [$requirements, $observations, $contentNodes] = $this->nodesToModels($this->getNodes($tiptap));
        $requirements = $requirements->mapWithKeys(function($requirement) {
            return [$requirement->id => ['passed' => $requirement->pivot->passed, 'order' => $requirement->pivot->order]];
        });
        $observations = $observations->mapWithKeys(function($observation) {
            return [$observation->id => ['order' => $observation->pivot->order]];
        });

        $this->quali->requirements()->sync($requirements);
        $this->quali->observations()->sync($observations);
        $this->quali->contentNodes()->delete();
        $this->quali->contentNodes()->createMany($contentNodes);
    }

    public function nodesToModels(Collection $nodes) {
        $order = 0;
        $requirements = [];
        $observations = [];
        $contentNodes = [];
        $nodes->each(function($node) use(&$order, &$requirements, &$observations, &$contentNodes) {
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
                    $observation = $this->quali->participant->observations()->find(data_get($node, 'attrs.id'));
                    if (!$observation) throw new ObservationNotFoundException();
                    $observation->pivot->order = $order;
                    $observations[] = $observation;
                    break;
                default:
                    $contentNodes[] = [ 'json' => json_encode($node), 'order' => $order ];
                    break;
            }
            $order++;
        });
        return [collect($requirements), collect($observations), collect($contentNodes)];
    }

    public static function createContentNodeJSON($paragraphText) {
        $content = $paragraphText ? ['content' =>  [[ 'type' => 'text', 'text' => $paragraphText ]]] : [];
        return json_encode(array_merge(['type' => 'paragraph'], $content));
    }

    protected function getAllContents() {
        if (! $this->allContents) $this->allContents = $this->qualiRequirements($this->quali)
            ->merge($this->qualiObservations($this->quali))
            ->merge($this->qualiContentNodes($this->quali));
        return $this->allContents;
    }

    /**
     * @param array $tiptap
     * @param string|null $nodeType
     * @return Collection
     */
    protected function getNodes(array $tiptap, $nodeType = null) {
        if ($nodeType === null) {
            return collect(data_get($tiptap, 'content', []));
        }
        return collect(data_get($tiptap, 'content', []))->filter(function($node) use($nodeType) {
            return $node['type'] === $nodeType;
        });
    }

    /**
     * Checks whether the requirements in the given tiptap formatted contents are the same set of requirements that are
     * assigned to the quali. Throws a RequirementsOutdatedException containing corrected tiptap formatted content if
     * there is a mismatch.
     *
     * @param array $tiptap
     * @return bool|Collection
     * @throws RequirementsOutdatedException
     */
    protected function checkRequirementsAreUpToDate(array $tiptap) {
        $requirementNodes = $this->getNodes($tiptap, 'requirement');
        $tiptapRequirementIds = $requirementNodes->pluck('attrs.id');
        $qualiRequirementIds = $this->quali->requirements()->pluck('requirement_id');

        if ($tiptapRequirementIds->sort()->values()->all() !== $qualiRequirementIds->sort()->values()->all()) {
            $stillValid = $requirementNodes->filter(function($node) use ($qualiRequirementIds) {
                $qualiRequirementIds->containsStrict($node['id']);
            });
            $missingRequirements = $this->quali->requirements()->whereNotIn('id', $tiptapRequirementIds)->get();
            $correctedContents = $stillValid->merge($missingRequirements);

            throw new RequirementsOutdatedException($correctedContents->toJson());
        }
        return false;
    }

    /**
     * @param Quali $quali
     * @return Collection
     */
    protected static function qualiRequirements(Quali $quali) {
        return $quali->requirements->map(function(Requirement $requirement) {
            return [
                'order' => $requirement->pivot->order,
                'type' => 'requirement',
                'attrs' => [
                    'id' => $requirement->id,
                    'passed' => $requirement->pivot->passed,
                ]
            ];
        });
    }

    /**
     * @param Quali $quali
     * @return Collection
     */
    protected static function qualiObservations(Quali $quali) {
        return $quali->observations->map(function(Observation $observation) {
            return [
                'order' => $observation->pivot->order,
                'type' => 'observation',
                'attrs' => [
                    'id' => $observation->id,
                ]
            ];
        });
    }

    /**
     * @param Quali $quali
     * @return Collection
     */
    protected static function qualiContentNodes(Quali $quali) {
        return $quali->contentNodes->map(function(QualiContentNode $contentNode) {
            return collect(json_decode($contentNode->json))
                ->merge([
                    'order' => $contentNode->order
                ])->all();
        });
    }
}
