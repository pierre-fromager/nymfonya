<?php

namespace App\Controllers\Api\V1;

use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Response;
use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use App\Model\Repository\Metro\Lines;
use App\Model\Repository\Metro\Stations;
use App\Component\Db\Core;
use App\Component\Model\Orm\Orm;

final class Metro extends AbstractApi implements IApi
{
    /**
     * Lines model
     *
     * @var Lines
     */
    protected $modelLines;


    /**
     * Stations model
     *
     * @var Stations
     */
    protected $modelStations;

    /**
     * db core
     *
     * @var Core
     */
    protected $dbCore;

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
        $this->dbCore = new Core($container);
    }

    /**
     * search lines
     *
     * @return Metro
     */
    final public function lines(): Metro
    {
        $this->response->setCode(Response::HTTP_OK)->setContent([
            Response::_ERROR => false,
            Response::_ERROR_MSG => '',
            'datas' => $this->getQueryResults(
                $this->modelLines->find(['*'], [])
            )
        ]);
        return $this;
    }

    /**
     * search stations
     *
     * @return Metro
     */
    final public function stations(): Metro
    {
        $this->response->setCode(Response::HTTP_OK)->setContent([
            Response::_ERROR => false,
            Response::_ERROR_MSG => '',
            'datas' => $this->getQueryResults(
                $this->modelStations->find(['*'], [])
            )
        ]);
        return $this;
    }

    /**
     * get query results
     *
     * @param Orm $query
     * @return array
     */
    protected function getQueryResults(Orm $query)
    {
        return $this->dbCore->fromOrm($query)->run(
            $query->getSql(),
            $query->getBuilderValues()
        )->hydrate()
            ->getRowset();
    }
}
