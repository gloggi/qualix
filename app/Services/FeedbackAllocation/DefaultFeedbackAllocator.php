<?php

namespace App\Services\FeedbackAllocation;

use Graphp\Algorithms\Flow;
use Graphp\Algorithms\MinimumCostFlow\SuccessiveShortestPath;
use Graphp\Graph\Exception\UnderflowException;
use Graphp\Graph\Graph;
use Graphp\Graph\Vertex;

class DefaultFeedbackAllocator implements FeedbackAllocator
{
    private Graph $graph;
    private EdgeAdder $edgeAdder;
    private Vertex $source;
    private Vertex $sink;
    private array $nameToVertexId;
    private array $vertexIdToName;
    private int $participantCount;

    function __construct()
    {
        $this->graph = new Graph();
        $this->edgeAdder = new EdgeAdder($this->graph);
        $this->source = $this->graph->createVertex(0);
        $this->sink = $this->graph->createVertex(1);
        $this->nameToVertexId = [];
        $this->vertexIdToName = [];
    }

    public function tryToAllocateFeedbacks(array $trainerCapacities, array $participantPreferences, int $numberOfWishes, array $forbiddenWishes, int $defaultPriority = 100): array
    {
        $this->createGraphFromInput($trainerCapacities, $participantPreferences, $numberOfWishes, $forbiddenWishes, $defaultPriority);
        return $this->calculateMaxFlowMinCost($this->participantCount);

    }

    function createGraphFromInput(array $trainerCapacities, array $participantPreferences, int $numberOfWishes, array $forbiddenWishes, int $defaultPriority = 100)
    {
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

            $this->nameToVertexId[$name] = $trainerVertexId;
            $this->vertexIdToName[$trainerVertexId] = $name;

            $this->edgeAdder->addEdge($trainerVertex, $this->sink, $capacity, 0);
        }

        // Add source to participants and handle preferences
        foreach ($participantPreferences as $index => $participantPreference) {
            $participantName = $participantPreference[0];
            $participantVertexId = $index + 2;
            $participantVertex = $this->graph->createVertex($participantVertexId);

            $this->nameToVertexId[$participantName] = $participantVertexId;
            $this->vertexIdToName[$participantVertexId] = $participantName;

            $this->edgeAdder->addEdge($this->source, $participantVertex, 1, 0);

            for ($priority = 1; $priority <= $numberOfWishes && $priority < count($participantPreference); $priority++) {
                $preferredTrainerName = $participantPreference[$priority];
                if ($preferredTrainerName !== 'x' && isset($this->nameToVertexId[$preferredTrainerName])) {
                    $trainerIndex = $this->nameToVertexId[$preferredTrainerName] - 2 - $participantCount;
                    $preferenceMatrix[$index][$trainerIndex] = min($priority, $preferenceMatrix[$index][$trainerIndex]);
                }
            }
        }

        // Handle forbidden wishes
        foreach ($forbiddenWishes as $forbiddenWish) {
            [$participantName, $trainerName] = $forbiddenWish;
            $participantIndex = $this->nameToVertexId[$participantName] - 2;
            $trainerIndex = $this->nameToVertexId[$trainerName] - 2 - $participantCount;
            $preferenceMatrix[$participantIndex][$trainerIndex] = PHP_INT_MAX;
        }

        // Create edges from participants to trainers if not forbidden
        foreach ($preferenceMatrix as $participantIndex => $trainerPriorities) {
            $participantVertexId = $participantIndex + 2;
            $participantVertex = $this->graph->getVertex($participantVertexId); // this->nameToVertex[$this->indexToParticipantName[$participantIndex]];
            foreach ($trainerPriorities as $trainerIndex => $priority) {
                if ($priority !== PHP_INT_MAX) {
                    $trainerVertexId = $trainerIndex + $participantCount + 2;
                    $trainerVertex = $this->graph->getVertex($trainerVertexId);
                    $this->edgeAdder->addEdge($participantVertex, $trainerVertex, 1, $priority);
                }
            }
        }
    }

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
        } catch (UnderflowException $e) {
            echo "Configuration Error: No feasible path from any participant to the sink. Check the setup.\n";
        } catch (\Exception $e) {
            echo "An error occurred: " . $e->getMessage() . "\n";
        }
        return [];
    }

    private function getAssignments(Graph $resultGraph): array
    {
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
                'trainerName' => $trainerName,
                'participantsNames' => $participants
            ];
        }
        return $assignments;
    }
}
