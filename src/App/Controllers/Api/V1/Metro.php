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
use App\Component\Filter;

final class Metro extends AbstractApi implements IApi
{
    const _NAME = 'name';
    const _LIMIT = 'limit';
    const _PAGE = 'page';
    const _DATAS = 'datas';

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
        $query = $this->search(
            $this->getFilteredInput(),
            $this->modelLines,
            Lines::_SRC
        );
        $this->response->setCode(Response::HTTP_OK)->setContent([
            Response::_ERROR => false,
            Response::_ERROR_MSG => '',
            self::_DATAS => $this->getQueryResults($query)
        ]);
        unset($query);
        return $this;
    }

    /**
     * search stations
     *
     * @return Metro
     */
    final public function stations(): Metro
    {
        $query = $this->search(
            $this->getFilteredInput(),
            $this->modelStations,
            Stations::_NAME
        );
        $this->response->setCode(Response::HTTP_OK)->setContent([
            Response::_ERROR => false,
            Response::_ERROR_MSG => '',
            self::_DATAS => $this->getQueryResults($query)
        ]);
        unset($query);
        return $this;
    }

    /**
     * get results as array
     *
     * @param Orm $query
     * @return array
     */
    protected function getQueryResults(Orm $query)
    {
        $sql = $query->getSql();
        return $this->dbCore->fromOrm($query)->run(
            $sql,
            $query->getBuilderValues()
        )->hydrate()->getRowset();
    }

    /**
     * search items by search key and value limiting results amount
     *
     * @param array $inputs
     * @param Orm $model
     * @param string $searchKey
     * @return Orm
     */
    protected function search(array $inputs, Orm &$model, string $searchKey): Orm
    {
        $searchValue = (isset($inputs[$searchKey]))
            ? Orm::SQL_WILD . $inputs[$searchKey] . Orm::SQL_WILD
            : Orm::SQL_WILD;
        $model->find([Orm::SQL_ALL], [$searchKey . Orm::OP_LIKE => $searchValue]);
        if (isset($inputs[self::_LIMIT])) {
            $model->getQuery()->limit(0, (int) $inputs[self::_LIMIT]);
        }
        return $model;
    }

    /**
     * return filtered request params
     *
     * @return array
     */
    protected function getFilteredInput(): array
    {
        return (new Filter($this->getParams(), [
            self::_NAME => FILTER_SANITIZE_STRING,
            self::_LIMIT => FILTER_SANITIZE_NUMBER_INT,
            self::_PAGE => FILTER_SANITIZE_NUMBER_INT,
        ]))->process()->toArray();
    }
}
