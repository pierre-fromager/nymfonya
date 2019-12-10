<?php

namespace App\Component\Math\Graph\Path;

/**
 * Weighted graph path finder using Floydwarshall method
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
class Floydwarshall
{

    const INFINITE = 66666;

    /**
     * Distances array
     * @var array
     */
    protected $dist;

    /**
     * Precedence matrix
     * @var array
     */
    protected $pred;

    /**
     * Weights array
     * @var array
     */
    protected $weights;

    /**
     * Number of nodes
     * @var integer
     */
    protected $nodeCount;

    /**
     * Node names list
     * @var array
     */
    protected $nodenames;

    /**
     * Temporary table for various stuff.
     * @var array
     */
    protected $tmp;

    /**
     * instanciate
     * @param array $graph Graph matrice.
     * @param array $nodenames Node names as an array.
     */
    public function __construct(array $graph, array $nodenames = [])
    {
        $this->reset();
        $this->weights = $graph;
        $this->nodeCount = count($this->weights);
        if (!empty($nodenames) && $this->nodeCount == count($nodenames)) {
            $this->nodenames = $nodenames;
        }
    }

    /**
     * reset
     *
     * @return Floydwarshall
     */
    protected function reset(): Floydwarshall
    {
        $this->tmp = [];
        $this->dist = [[]];
        $this->pred = [[]];
        return $this;
    }

    /**
     * populate then calculate distance and precedence
     */
    public function process(): Floydwarshall
    {
        $this->populate();
        for ($k = 0; $k < $this->nodeCount; $k++) {
            for ($i = 0; $i < $this->nodeCount; $i++) {
                for ($j = 0; $j < $this->nodeCount; $j++) {
                    $nextDist = $this->dist[$i][$k] + $this->dist[$k][$j];
                    if ($this->dist[$i][$j] > $nextDist) {
                        $this->dist[$i][$j] = $nextDist;
                        $this->pred[$i][$j] = $this->pred[$k][$j];
                    }
                }
            }
        }
        return $this;
    }

    /**
     * populate graph nodes to
     * set distances matrix from weights
     * and set matrix precedences
     *
     * @return Floydwarshall
     */
    protected function populate(): Floydwarshall
    {
        for ($i = 0; $i < $this->nodeCount; $i++) {
            for ($j = 0; $j < $this->nodeCount; $j++) {
                if ($i == $j) {
                    $this->dist[$i][$j] = 0;
                } elseif (isset($this->weights[$i][$j]) && $this->weights[$i][$j] > 0) {
                    $this->dist[$i][$j] = $this->weights[$i][$j];
                } else {
                    $this->dist[$i][$j] = self::INFINITE;
                }
                $this->pred[$i][$j] = $i;
            }
        }
        return $this;
    }

    /**
     * return distance matrix
     *
     * @return array
     */
    public function getDistances(): array
    {
        return $this->dist;
    }

    /**
     * return precedence matrix
     *
     * @return array
     */
    public function getPrecedence(): array
    {
        return $this->pred;
    }
}
