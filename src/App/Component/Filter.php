<?php

/**
 * Component Filter
 *
 * filter an array matching args
 *
 * @author pierrefromager
 */

namespace App\Component;

class Filter
{
    const INPUT_FILTER_FILTER = 'filter';
    const INPUT_FILTER_OPTIONS = 'options';
    const INPUT_FILTER_PROCESS = 'process';

    private $filterArgs;
    private $data;
    private $prepared;
    private $result;

    /**
     * __construct
     *
     * @param array $data
     * @param array $filterArgs
     */
    public function __construct(array $data, array $filterArgs)
    {
        $this->data = $data;
        $this->filterArgs = $filterArgs;
        return $this;
    }

    /**
     * process result filter
     *
     * @return Filter
     */
    public function process(): Filter
    {
        $this->prepare();
        $this->result = \filter_var_array($this->data, $this->prepared);
        return $this;
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return $this->result;
    }

    /**
     * prepare datas
     *
     * @return Filter
     */
    protected function prepare(): Filter
    {
        $this->prepared = [];
        foreach ($this->filterArgs as $k => $v) {
            if (is_object($v)) {
                $this->prepared[$k] = [
                    self::INPUT_FILTER_FILTER => FILTER_CALLBACK,
                    self::INPUT_FILTER_OPTIONS => [$v, self::INPUT_FILTER_PROCESS]
                ];
            } else {
                $this->prepared[$k] = $v;
            }
        }
        unset($this->filterArgs);
        return $this;
    }
}
