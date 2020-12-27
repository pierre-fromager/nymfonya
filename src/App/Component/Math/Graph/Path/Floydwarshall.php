<?php

declare(strict_types=1);

namespace App\Component\Math\Graph\Path;

/**
 * Weighted graph path finder using Floydwarshall method.
 * Constructor expects a weighted square matrix
 * and a nodes name collection to indentify nodes by name if required.
 *
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
    private $dist;

    /**
     * Precedence matrix
     * @var array
     */
    private $pred;

    /**
     * Weights array
     * @var array
     */
    private $weights;

    /**
     * Number of nodes
     * @var integer
     */
    private $nodeCount;

    /**
     * Node names list
     * @var array
     */
    private $nodeNames;

    /**
     * path
     * @var array
     */
    private $path;

    /**
     * instanciate
     * @param array $weightedMatrix graph weighted square matrice.
     * @param array $nodeNames nodes names as array.
     */
    public function __construct(array $weightedMatrix, array $nodeNames = [])
    {
        $this->reset();
        $this->weights = $weightedMatrix;
        $this->nodeCount = count($this->weights);
        if (!empty($nodeNames) && $this->nodeCount == count($nodeNames)) {
            $this->nodeNames = $nodeNames;
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
        $srcIdx = array_search($src, $this->nodeNames);
        $dstIdx = array_search($dst, $this->nodeNames);
        $this->path = [];
        $this->searchPath($srcIdx, $dstIdx);
        $path = ($withNames)
            ? array_map(function ($v) {
                return $this->nodeNames[$v];
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
     * return node name from row integer
     *
     * @param integer $i
     * @return string
     */
    public function nodeName(int $i): string
    {
        return (isset($this->nodeNames[$i])) ? $this->nodeNames[$i] : '';
    }

    /**
     * return distance between two nodes identified by row/col couple.
     * if one member of couple is undefined infinite value is returned.
     *
     * @return float
     */
    public function getDistance(int $i, int $j): float
    {
        return (isset($this->dist[$i][$j])) ? $this->dist[$i][$j] : self::INFINITE;
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
