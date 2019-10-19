<?php

namespace App\Model;

use App\Http\Request;

/**
 * Class App\Model\AbstractSearch
 *
 * is abstract search class from csv based file
 *
 */
abstract class AbstractSearch
{

    /**
     * filename to read from
     *
     * @var String
     */
    protected $filename;

    /**
     * stack
     *
     * @var array
     */
    protected $datas;

    /**
     * filter
     *
     * @var String
     */
    protected $filter;

    /**
     * field separator
     *
     * @var String
     */
    protected $separator;

    /**
     * request
     *
     * @var Request
     */
    protected $req;

     /**
      * instanciate
      *
      * @param Request $r
      */
    public function __construct(Request $req)
    {
        $this->req = $req;
        $this->datas = [];
        return $this;
    }

    /**
     * set file name
     *
     * @param string $filename
     * @return Search
     */
    public function setFilename(string $filename): AbstractSearch
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * set regex filter
     *
     * @param string $filter
     * @return Search
     */
    public function setFilter(string $filter): AbstractSearch
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * set field separator
     *
     * @param string $separator
     * @return Search
     */
    public function setSeparator(string $separator): AbstractSearch
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * stack items from streamed asset file
     *
     * @return Search
     */
    public function readFromStream(): AbstractSearch
    {
        $stream = new \SplFileObject($this->filename);
        $lines = new \RegexIterator($stream, $this->filter);
        foreach ($lines as $line) {
            $data = explode($this->separator, $line);
            $this->setItem($data);
        }
        unset($lines, $stream);
        return $this;
    }

    /**
     * returns unfiltered datas
     *
     * @return array
     */
    public function get(): array
    {
        return $this->datas;
    }

    /**
     * add route item to stack from dat
     *
     * @param array $data
     * @return AbstractSearch
     */
    abstract protected function setItem(array $data): AbstractSearch;
}
