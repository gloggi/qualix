<?php

namespace App\Services\FeedbackAllocation;

use App\Exceptions\FeedbackAllocationException;
use Graphp\Algorithms\Flow;
use Graphp\Algorithms\MinimumCostFlow\SuccessiveShortestPath;
use Graphp\Graph\Graph;
use Graphp\Graph\Vertex;
use Illuminate\Support\Facades\Log;
use UnexpectedValueException;


class DefaultFeedbackAllocator implements FeedbackAllocator {
    private Graph $graph;
    private Vertex $source;
    private Vertex $sink;
    private array $trainerNameToVertexId;
    private array $particpantNameToVertexId;
    private array $vertexIdToName;
    private int $participantCount;

    function __construct() {
        $this->graph = new Graph();
        $this->source = $this->graph->createVertex(0);
        $this->sink = $this->graph->createVertex(1);
        $this->trainerNameToVertexId = [];
        $this->particpantNameToVertexId = [];
        $this->vertexIdToName = [];
    }

    public function tryToAllocateFeedbacks(array $trainerCapacities, array $participantPreferences, int $numberOfWishes, array $forbiddenWishes, int $defaultPriority = 100, bool $unweighted = false): array {
        $this->createGraphFromInput($trainerCapacities, $participantPreferences, $numberOfWishes, $forbiddenWishes, $defaultPriority, $unweighted);
        return $this->calculateMaxFlowMinCost($this->participantCount);
    }

    function createGraphFromInput(array $trainerCapacities, array $participantPreferences, int $numberOfWishes, array $forbiddenWishes, int $defaultPriority = 100, bool $unweighted = false) {
        $trainerCount = count($trainerCapacities);
        $participantCount = count($participantPreferences);
        $this->participantCount = $participantCount;
        $this->source->setBalance($participantCount);
        $this->sink->setBalance(-$participantCount);

        // Initialize preference matrix
        $preferenceMatrix = array_fill(0, $participantCount, array_fill(0, $trainerCount, $defaultPriority));

        // Add trainers to the sink with capacity
        foreach ($trainerCapacities as $index => $trainer) {
            [$name, $capacity] = $trainer;
            $trainerVertexId = $participantCount + $index + 2;
            $trainerVertex = $this->graph->createVertex($trainerVertexId);

            $this->trainerNameToVertexId[$name] = $trainerVertexId;
            $this->vertexIdToName[$trainerVertexId] = $name;

            $this->addEdge($trainerVertex, $this->sink, $capacity, 0);
        }

        // Add source to participants and handle preferences
        foreach ($participantPreferences as $index => $participantPreference) {
            $participantName = $participantPreference[0];
            $participantVertexId = $index + 2;
            $participantVertex = $this->graph->createVertex($participantVertexId);

            $this->particpantNameToVertexId[$participantName] = $participantVertexId;
            $this->vertexIdToName[$participantVertexId] = $participantName;

            $this->addEdge($this->source, $participantVertex, 1, 0);

            for ($priority = 1; $priority <= $numberOfWishes && $priority < count($participantPreference); $priority++) {
                $preferredTrainerName = $participantPreference[$priority];
                if ($preferredTrainerName !== null && isset($this->trainerNameToVertexId[$preferredTrainerName])) {
                    $trainerIndex = $this->trainerNameToVertexId[$preferredTrainerName] - 2 - $participantCount;
                    $weight = $unweighted ? 1 : $priority;
                    $preferenceMatrix[$index][$trainerIndex] = min($weight, $preferenceMatrix[$index][$trainerIndex]);
                }
            }
        }

        // Handle forbidden wishes
        foreach ($forbiddenWishes as $forbiddenWish) {
            [$participantName, $trainerName] = $forbiddenWish;
            $participantIndex = $this->particpantNameToVertexId[$participantName] - 2;
            $trainerIndex = $this->trainerNameToVertexId[$trainerName] - 2 - $participantCount;
            $preferenceMatrix[$participantIndex][$trainerIndex] = PHP_INT_MAX;
        }

        // Create edges from participants to trainers if not forbidden
        foreach ($preferenceMatrix as $participantIndex => $trainerPriorities) {
            $participantVertexId = $participantIndex + 2;
            $participantVertex = $this->graph->getVertex($participantVertexId);
            foreach ($trainerPriorities as $trainerIndex => $priority) {
                if ($priority !== PHP_INT_MAX) {
                    $trainerVertexId = $trainerIndex + $participantCount + 2;
                    $trainerVertex = $this->graph->getVertex($trainerVertexId);
                    $this->addEdge($participantVertex, $trainerVertex, 1, $priority);
                }
            }
        }
    }

    private function addEdge(Vertex $from, Vertex $to, int $capacity, int $cost) {
        $edge = $this->graph->createEdgeDirected($from, $to);
        $edge->setWeight($cost);
        $edge->setCapacity($capacity);
    }

    /**
     * @throws FeedbackAllocationException
     */
    private function calculateMaxFlowMinCost(int $numberOfFeedbacks): array
    {
        try {
            $successiveShortestPath = new SuccessiveShortestPath($this->graph);
            $resultGraph = $successiveShortestPath->createGraph();

            $source = $resultGraph->getVertex($this->source->getId());
            $flowGraph = new Flow($resultGraph);
            $maxFlow = $flowGraph->getFlowVertex($source);

            if ($maxFlow === $numberOfFeedbacks) {
                return $this->getAssignments($resultGraph);
            }

            throw new UnexpectedValueException('Unlösbare Zuteilung – unvollständiger Flow');
        } catch (UnexpectedValueException $e) {
            if (str_contains($e->getMessage(), 'not enough capacity') ||
                str_contains($e->getMessage(), 'Unlösbare Zuteilung')) {
                throw new FeedbackAllocationException('t.views.admin.feedbacks.allocation.errors.allocation_failed');
            }

            Log::error('Graph-Fehler bei der Zuteilung', [
                'type' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            throw new FeedbackAllocationException('t.views.admin.feedbacks.allocation.errors.unexpected');

        } catch (\Throwable $e) {
            Log::error('Allgemeiner Fehler in der Feedback-Zuteilung', [
                'type' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            throw new FeedbackAllocationException('t.views.admin.feedbacks.allocation.errors.unexpected');

        }
    }

    private function getAssignments(Graph $resultGraph): array {
        $assignments = [];
        $trainerParticipants = [];

        $sinkId = $this->sink->getId();
        $edgesToSink = $resultGraph->getVertex($sinkId)->getEdgesIn();
        foreach ($edgesToSink as $edge) {
            $trainerVertex = $edge->getVertexStart();
            $trainerName = $this->vertexIdToName[$trainerVertex->getId()];
            foreach ($trainerVertex->getEdgesIn() as $edgeIn) {
                $vertexIn = $edgeIn->getVertexStart();
                if ($vertexIn->getId() !== $sinkId && $edgeIn->getFlow() > 0) {
                    $participantName = $this->vertexIdToName[$vertexIn->getId()];
                    $trainerParticipants[$trainerName][] = $participantName;
                }
            }

        }

        foreach ($trainerParticipants as $trainerName => $participants) {
            $assignments[] = [
                'trainerIdent' => $trainerName,
                'participantIdents' => $participants
            ];
        }
        return $assignments;
    }
}
