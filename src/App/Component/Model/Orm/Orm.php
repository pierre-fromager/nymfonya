<?php

namespace App\Component\Model\Orm;

use App\Config;
use App\Container;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;

/**
 * Poor man orm
 */
class Orm implements IOrm
{

    /**
     * config
     *
     * @var Config
     */
    protected $config;

    /**
     * what fields
     * @var array
     */
    protected $what;

    /**
     * where criterias
     * @var array
     */
    protected $where;

    /**
     * table name
     * @var string
     */
    protected $tablename;

    /**
     * table primary key
     * @var string
     */
    protected $primary;

    /**
     * table name
     * @var string
     */
    protected $dbname;

    /**
     * pool name
     * @var string
     */
    protected $poolname;

    /**
     * query builder instance
     * @var GenericBuilder
     * @see https://github.com/nilportugues/php-sql-query-builder
     */
    protected $queryBuilder;

    /**
     * query from builder
     * @var object
     */
    protected $query;

    /**
     * instanciate
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->config = $container->getService(Config::class);
        $this->queryBuilder = new GenericBuilder();
        $this->query = null;
        return $this;
    }

    /**
     * find a record with what field matching where criterias
     * @param array $what
     * @param array $where
     */
    public function find(array $what = [], array $where = []): Orm
    {
        $this->what = $what;
        $this->where = $where;
        $this->query = new Select();
        $this->build();
        return $this;
    }

    /**
     * count records matching where criterias.
     * aliases is formely [columnn => column_alias]
     *
     * @param array $where
     * @param array $aliases
     * @return Orm
     */
    public function count(array $where = [], array $aliases = []): Orm
    {
        $this->where = $where;
        $this->query = new Select();
        if (empty($aliases)) {
            $this->query->count();
        } else {
            reset($aliases);
            $fistKey = key($aliases);
            $aliasValue = $aliases[$fistKey];
            $this->query->count($fistKey, $aliasValue);
        }
        $this->buildWhere();
        return $this;
    }

    /**
     * update a record with what fields matching where criterias
     * @param array $what
     * @param array $where
     */
    public function update(array $what = [], array $where = []): Orm
    {
        $this->what = $what;
        $this->where = $where;
        $this->query = new Update();
        $this->build();
        return $this;
    }

    /**
     * insert record with what fields
     * @param array $what
     */
    public function insert(array $what = []): Orm
    {
        $this->what = $what;
        $this->query = new Insert();
        $this->build();
        return $this;
    }

    /**
     * delete matching where criterias
     * @param array $where
     */
    public function delete(array $where = []): Orm
    {
        $this->where = $where;
        $this->query = new Delete();
        $this->build();
        return $this;
    }

    /**
     * builder instance
     * @return GenericBuilder
     */
    public function getQueryBuilder(): GenericBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * query instance
     * @return object
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * query sql string
     * @return string
     */
    public function getSql()
    {
        return $this->queryBuilder->write($this->query);
    }

    /**
     * build sql query
     *
     * @return Orm
     */
    protected function build(): Orm
    {
        if (false === is_object($this->query)) {
            throw new \Exception('Build : Invalid query instance');
        }
        $queryClassname = get_class($this->query);
        if (false === class_exists($queryClassname)) {
            throw new \Exception('Build : Invalid query type');
        }
        $this->query->setTable($this->tablename);
        switch ($queryClassname) {
            case Select::class:
                $this->query->setColumns($this->what);
                break;
            case Update::class:
                if (empty($this->what)) {
                    throw new \Exception(
                        'Build : Update requires not empty payload'
                    );
                }
                if (empty($this->where)) {
                    throw new \Exception(
                        'Build : Update requires at least one condition'
                    );
                }
                $this->query->setValues($this->what);
                break;
            case Insert::class:
                if (empty($this->what)) {
                    throw new \Exception(
                        'Build : Insert requires not empty payload'
                    );
                }
                $this->query->setValues($this->what);
                break;
            case Delete::class:
                if (empty($this->where)) {
                    throw new \Exception(
                        'Build : Delete requires at least one condition'
                    );
                }
                break;
            default:
                break;
        }
        $this->buildWhere();
        return $this;
    }

    /**
     * build where condition on query
     *
     * @return Orm
     */
    protected function buildWhere(): Orm
    {
        if (false === empty($this->where)) {
            foreach ($this->where as $k => $v) {
                $whereOperator = $this->getWhereOperator($k, $v);
                $this->query->where()->{$whereOperator}($k, $v);
            }
        }
        return $this;
    }

    /**
     * check where condition ket values to find operators
     *
     * @param string $whereColumn
     * @return string
     */
    protected function getWhereOperator(string &$whereColumn, $value): string
    {
        $hasArray = is_array($value);
        $operator = $whereColumn[strlen($whereColumn) - 1];
        $hasOperator = in_array($operator, self::OPERATORS);
        if (false === $hasOperator) {
            return ($hasArray) ? 'in' : 'equals';
        }
        foreach (self::OPERATORS as $op) {
            $whereColumn = str_replace($op, '', $whereColumn);
        }
        switch ($operator) {
            case '!':
                return ($hasArray) ? 'notIn' : 'notEquals';
                break;
            case '<':
                return 'lessThan';
                break;
            case '>':
                return 'greaterThan';
                break;
            case '#':
                return 'like';
                break;
        }
    }
}
