<?php

namespace App\Services\FeedbackAllocation;


use Graphp\Graph\Graph;
use Graphp\Graph\Vertex;

class EdgeAdder
{
    private $graph;

    /**
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * @param Vertex $from
     * @param Vertex $to
     * @param Integer $capacity
     * @param Integer $cost
     */
    public function addEdge(Vertex $from, Vertex $to, int $capacity, int $cost)
    {
        $edge = $this->graph->createEdgeDirected($from, $to);
        $edge->setWeight($cost);
        $edge->setCapacity($capacity);
    }
}
