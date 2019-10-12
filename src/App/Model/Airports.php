<?php

namespace App\Model\Airlines;

use App\Model\Airlines\Search;
use App\Http\Request;

/**
 * Class App\Model\Airlines\Airports
 *
 * Provides airports list.
 *
 * @see http://arm.64hosts.com/
 *
 */
class Airports extends Search
{
    const _CSV = 'csv';
    const _ID = 'id';
    const _LON = 'lon';
    const _LAT = 'lat';
    const _NAME = 'name';
    const AIRPORT_FILENAME = '/assets/airports.' . self::_CSV;
    const FIELD_SEPARATOR = "\t";

    /**
     * instanciate
     *
     */
    public function __construct(Request $req)
    {
        parent::__construct($req);
        $this->setFilename($this->getAppPath($req) . self::AIRPORT_FILENAME);
        $this->separator = self::FIELD_SEPARATOR;
        return $this;
    }

    /**
     * add airport item to stack
     *
     * @param array $data
     * @return Search
     */
    protected function setItem(array $data): Search
    {
        if (count($data) < 3) {
            //var_dump($data);die;
        }
        

        $name = preg_replace('/\s+/', ' ', $data[3]);
        $this->datas[] = [
            self::_ID => $data[0],
            self::_LAT => (float) $data[1],
            self::_LON => (float) $data[2],
            self::_NAME => ltrim(rtrim($name))
        ];
        return $this;
    }

    /**
     * returns app path from request
     *
     * @param Request $req
     * @return string
     */
    private function getAppPath(Request $req): string
    {
        return dirname(dirname($req->getFilename()));
    }
}
