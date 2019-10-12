<?php

namespace App\Model\Airlines;

/**
 * Class App\Model\Airlines\Routes
 *
 * Provides airlines routes from 2 differents source files.
 *
 * @see https://openflights.org/data.html#route
 * @see https://github.com/jpatokal/openflights/blob/master/data/routes.dat
 * @see http://arm.64hosts.com/
 *
 * output item model :
 * - cid is companyId 2 letters IATA
 * - from is airport 3 letters IATA
 * - to is airport 3 letters IATA
 */
class Routes
{
    const _EXT = 'dat';
    const _ID = 'id';
    const _ORIGIN = 'from';
    const _DESTINATION = 'to';
    const _COMPANY_ID = 'cid';
    const ROUTE_FILE_PATH = '/../../../../assets/';
    const ROUTE_FILENAME = 'routes.' . self::_EXT;
    const RELATIVE_PATH = self::ROUTE_FILE_PATH . self::ROUTE_FILENAME;
    const FIELD_SEPARATOR = "\t";
    const AGG = '-';

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
     * instanciate
     *
     */
    public function __construct()
    {
        $this->datas = [];
        $this->setFilename(self::RELATIVE_PATH);
        $this->separator = self::FIELD_SEPARATOR;
        return $this;
    }

    /**
     * destroy instance
     *
     * @param string $companyId
     */
    public function __destruct()
    {
        $this->datas = [];
        $this->companyId = '';
    }

    /**
     * set filename
     *
     * @param string $filename
     * @return Routes
     */
    public function setFilename(string $filename): Routes
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * set regex filter
     *
     * filter groups applied on csv
     * Model :
     * airline,from,to,codeshare,stops,equipment
     * Ex :
     * ^(..)\t(...)\t(ORY)\t(.*)\t(.*)\t(.*)$
     *
     * @param string $filter
     * @return Routes
     */
    public function setFilter(string $filter): Routes
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * set field separator
     *
     * @param string $separator
     * @return Routes
     */
    public function setSeparator(string $separator): Routes
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * stack items from streamed asset file
     *
     * @return Routes
     */
    public function readFromStream(): Routes
    {
        $stream = new \SplFileObject(__DIR__ . $this->filename);
        $lines = new \RegexIterator($stream, $this->filter);
        foreach ($lines as $line) {
            $data = explode($this->separator, $line);
            $this->setItem($data);
        }
        unset($lines);
        unset($stream);
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
     * @return Routes
     */
    protected function setItem(array $data): Routes
    {
        $item = [];
        $item[self::_COMPANY_ID] = $data[0];
        $item[self::_ID] = $data[1] . self::AGG . $data[2];
        $item[self::_ORIGIN] = [];
        $item[self::_ORIGIN][self::_ID] = $data[1];
        $item[self::_DESTINATION] = [];
        $item[self::_DESTINATION][self::_ID] = $data[2];
        $this->datas[] = $item;
        $item[self::_ID] = $data[2] . self::AGG . $data[1];
        $item[self::_ORIGIN][self::_ID] = $data[2];
        $item[self::_DESTINATION][self::_ID] = $data[1];
        $this->datas[] = $item;
        return $this;
    }
}
