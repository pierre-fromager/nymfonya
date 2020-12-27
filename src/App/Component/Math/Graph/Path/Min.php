<?php

namespace App\Component\Math\Graph\Path;

/**
 * Find least number of hops between nodes collection.
 * Graph should be considered as oriented.
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
class Min
{
    /**
     * graph
     *
     * @var array
     */
    protected $graph;

    /**
     * visited
     *
     * @var array
     */
    protected $visited = [];

    /**
     * instanciate
     *
     * @param array $graph
     */
    public function __construct(array $graph)
    {
        $this->graph = $graph;
    }

    /**
     * return path search as array
     *
     * @param string  $origin
     * @param string  $destination
     * @return array
     */
    public function path(string $origin, string $destination): array
    {
        // check exists origin destination
        if (!isset($this->graph[$origin]) || !isset($this->graph[$destination])) {
            return [];
        }
        // mark all nodes as unvisited
        foreach ($this->graph as $vertex => $adj) {
            $this->visited[$vertex] = false;
        }
        // create an empty queue
        $q = new \SplQueue();
        // enqueue the origin vertex and mark as visited
        $q->enqueue($origin);
        $this->visited[$origin] = true;
        // this is used to track the path back from each node
        $path = [];
        $path[$origin] = new \SplDoublyLinkedList();
        $path[$origin]->setIteratorMode(
            \SplDoublyLinkedList::IT_MODE_FIFO | \SplDoublyLinkedList::IT_MODE_KEEP
        );
        $path[$origin]->push($origin);
        // while queue is not empty and destination not found
        while (!$q->isEmpty() && $q->bottom() != $destination) {
            $t = $q->dequeue();
            if (!empty($this->graph[$t])) {
                // for each adjacent neighbor
                foreach ($this->graph[$t] as $vertex) {
                    if (!$this->visited[$vertex]) {
                        // if not yet visited, enqueue vertex and mark
                        // as visited
                        $q->enqueue($vertex);
                        $this->visited[$vertex] = true;
                        // add vertex to current path
                        $path[$vertex] = clone $path[$t];
                        $path[$vertex]->push($vertex);
                    }
                }
            }
        }
        return (isset($path[$destination])) ? iterator_to_array($path[$destination]) : [];
    }
}
