<?php

namespace App\Controllers\Api\V1;

use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Response;
use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use App\Model\Repository\Metro\Lines;
use App\Model\Repository\Metro\Stations;

final class Metro extends AbstractApi implements IApi
{
    protected $modelLines;
    protected $modelStations;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->modelLines = new Lines($container);
        $this->modelStations = new Stations($container);
    }

    /**
     * search lines
     *
     * @return Test
     */
    final public function lines(): Metro
    {
        $this->response->setCode(Response::HTTP_OK)->setContent([
            Response::_ERROR => false,
            Response::_ERROR_MSG => '',
            'datas' => [
                'lines' => []
            ]
        ]);
        return $this;
    }

    /**
     * search stations
     *
     * @return Test
     */
    final public function stations(): Metro
    {
        $this->response->setCode(Response::HTTP_OK)->setContent([
            Response::_ERROR => false,
            Response::_ERROR_MSG => '',
            'datas' => [
                'stations' => []
            ]
        ]);
        return $this;
    }
}
