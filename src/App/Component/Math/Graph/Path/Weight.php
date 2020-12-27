<?php

namespace App\Component\Math\Graph\Path;

/**
 * Dijikstra path search for weighted graph
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
class Weight
{
    const _INF = 99999;

    /**
     * graph
     *
     * @var array
     */
    protected $graph;

    /**
     * source
     *
     * @var String
     */
    private $src;

    /**
     * destination
     *
     * @var String
     */
    private $dst;

    /**
     * path
     *
     * @var array
     */
    private $path = [];

    /**
     * the nearest path with its parent and weight
     *
     * @var array
     */
    private $s = [];

    /**
     * the left nodes without the nearest path
     *
     * @var array
     */
    private $q = [];

    /**
     * __construct
     *
     * @param array $graph
     */
    public function __construct($graph)
    {
        $this->graph = $graph;
    }

    /**
     * path
     *
     * @param string $src
     * @param string $dst
     * @return array
     */
    public function path(string $src, string $dst): array
    {
        $this->init($src, $dst);
        $this->path = [];
        if (isset($this->graph[$this->src]) && isset($this->graph[$this->dst])) {
            $this->search()->processPath();
        }
        return $this->path;
    }

    /**
     * distance
     *
     * @return float
     */
    public function distance(): float
    {
        return ($this->path) ? $this->s[$this->dst][1] : 0;
    }

    /**
     * start calculating
     *
     * @return Weight
     */
    protected function search(): Weight
    {
        while (!empty($this->q)) {
            $min = $this->min();
            if ($min == $this->dst) {
                break;
            }
            $keys = array_keys($this->graph[$min]);
            $kam = count($keys);
            for ($c = 0; $c < $kam; $c++) {
                $k = $keys[$c];
                $v = $this->graph[$min][$k];
                if (!empty($this->q[$k])) {
                    if (($checkMin = $this->q[$min] + $v) < $this->q[$k]) {
                        $this->q[$k] = $checkMin;
                        $this->s[$k] = [$min, $this->q[$k]];
                    }
                }
            }
            unset($this->q[$min]);
        }
        return $this;
    }

    /**
     * lowest weighted node name
     *
     * @return string
     */
    protected function min(): string
    {
        return array_search(min($this->q), $this->q);
    }

    /**
     * init queue assuming all edges are bi-directional
     *
     * @param string $src
     * @param string $dst
     * @return Weight
     */
    protected function init(string $src, string $dst): Weight
    {
        $this->src = $src;
        $this->dst = $dst;
        $this->s = [];
        $this->q = [];
        $keys = array_keys($this->graph);
        foreach ($keys as $key) {
            $this->q[$key] = self::_INF;
        }
        $this->q[$this->src] = 0;
        return $this;
    }

    /**
     * set path
     *
     * @return Weight
     */
    protected function processPath(): Weight
    {
        $this->path = [];
        $pos = $this->dst;
        while ($pos != $this->src) {
            $this->path[] = $pos;
            $pos = $this->s[$pos][0];
        }
        $this->path[] = $this->src;
        $this->path = array_reverse($this->path);
        return $this;
    }
}
