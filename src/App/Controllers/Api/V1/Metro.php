<?php

declare(strict_types=1);

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
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

final class Metro extends AbstractApi implements IApi
{

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
            Lines::_HSRC,
            '',
            $this->modelLines
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
            Stations::_NAME,
            Orm::OP_LIKE,
            $this->modelStations
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
    protected function getQueryResults(Orm $query): array
    {
        return $this->dbCore
            ->fromOrm($query)
            ->run($query->getSql(), $query->getBuilderValues())
            ->hydrate()
            ->getRowset();
    }

    /**
     * search items by search key and value limiting results amount
     *
     * @param array $inputs
     * @param string $searchKey
     * @param string $operator
     * @param Orm $model
     * @return Orm
     */
    protected function search(array $inputs, string $searchKey, string $operator, Orm &$model): Orm
    {
        $where = [];
        if (isset($inputs[$searchKey])) {
            $searchValue = $inputs[$searchKey];
            if ($operator === Orm::OP_LIKE) {
                $where = [
                    $searchKey . $operator => Orm::SQL_WILD . $searchValue . Orm::SQL_WILD
                ];
            } else {
                $where = [$searchKey => $searchValue];
            }
        }
        $model->find([Orm::SQL_ALL], $where);
        if ([] === $where && !isset($inputs[self::_LIMIT])) {
            $inputs[self::_LIMIT] = 10;
        }
        if (isset($inputs[self::_LIMIT])) {
            if ($model->getQuery() instanceof Select) {
                $page = isset($inputs[self::_PAGE]) ? (int) $inputs[self::_PAGE] : 0;
                $offset = $page * (int) $inputs[self::_LIMIT];
                $model->getQuery()->limit($offset, (int) $inputs[self::_LIMIT]);
            }
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
            Lines::_HSRC => FILTER_SANITIZE_STRING,
            Stations::_NAME => FILTER_SANITIZE_STRING,
            self::_LIMIT => FILTER_SANITIZE_NUMBER_INT,
            self::_PAGE => FILTER_SANITIZE_NUMBER_INT,
        ]))->process()->toArray();
    }
}
