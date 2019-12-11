<?php

namespace App\Component\Math\Graph\Path;

/**
 * Weighted graph path finder using Floydwarshall method
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
class Floydwarshall
{

    /**
     * max nodes
     */
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
     * path.
     * @var array
     */
    protected $path;

    /**
     * instanciate
     * @param array $weightedMatrix graph weighted square matrice.
     * @param array $nodenames nodes names as array.
     */
    public function __construct(array $weightedMatrix, array $nodenames = [])
    {
        $this->reset();
        $this->weights = $weightedMatrix;
        $this->nodeCount = count($this->weights);
        if (!empty($nodenames) && $this->nodeCount == count($nodenames)) {
            $this->nodenames = $nodenames;
        }
    }

    /**
     * populate from square matrix then calculate distance and precedence
     */
    public function process(): Floydwarshall
    {
        $this->reset();
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
     * return path
     *
     * @param string $src
     * @param string $dst
     * @param boolean $withNames
     * @return array
     */
    public function path(string $src, string $dst, bool $withNames = false): array
    {
        $srcIdx = array_search($src, $this->nodenames);
        $dstIdx = array_search($dst, $this->nodenames);
        $this->path = [];
        $this->searchPath($srcIdx, $dstIdx);
        $path = ($withNames)
            ? array_map(function ($v) {
                return $this->nodenames[$v];
            }, $this->path)
            : $this->path;
        return $path;
    }

    /**
     * recursive search for node path
     *
     * @param integer $i
     * @param integer $j
     * @return Floydwarshall
     */
    protected function searchPath(int $i, int $j): Floydwarshall
    {
        if ($i != $j) {
            $pred = $this->pred[$i][$j];
            $this->searchPath($i, $pred);
        }
        $this->path[] = $j;
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
    public function getPrecedences(): array
    {
        return $this->pred;
    }

    /**
     * reset
     *
     * @return Floydwarshall
     */
    protected function reset(): Floydwarshall
    {
        $this->path = [];
        $this->dist = [[]];
        $this->pred = [[]];
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
}
