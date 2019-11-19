<?php

namespace App\Component\Model\Orm;

use App\Component\Config;
use App\Component\Container;
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
     * service container
     *
     * @var Container
     */
    private $container;

    /**
     * config
     *
     * @var Config
     */
    protected $config;

    /**
     * columns fields
     * @var array
     */
    protected $columns;

    /**
     * where criterias
     * @var array
     */
    protected $where;

    /**
     * sort order
     * @var array
     */
    protected $order;

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
     * database name
     * @var string
     */
    protected $database;

    /**
     * db slot pool name
     * @var string
     */
    protected $slot;

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
        $this->container = $container;
        $this->config = $container->getService(Config::class);
        $this->queryBuilder = new GenericBuilder();
        $this->query = null;
        $this->columns = [];
        $this->where = [];
        return $this;
    }

    /**
     * set required columns
     *
     * @param array $columns
     * @return Orm
     */
    public function setColumns(array $columns): Orm
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * find a record with columns field matching where criterias
     * @param array $columns
     * @param array $where
     * @param array $order
     */
    public function find(array $columns = [], array $where = [], array $order = []): Orm
    {
        $this->where = $where;
        $order = (empty($order)) ? [$this->primary => 'DESC'] : $order;
        $this
            ->setColumns($columns)
            ->setQuery(new Select())
            ->build($this->tablename, $this->columns, $this->where)
            ->setOrder($order);
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
        $this->setQuery(new Select());
        if (empty($aliases)) {
            $this->query->count();
        } else {
            reset($aliases);
            $fistKey = key($aliases);
            $aliasValue = $aliases[$fistKey];
            $this->query->count($fistKey, $aliasValue);
        }
        return $this->buildWhere($this->where);
    }

    /**
     * update a record with columns fields matching where criterias
     * @param array $columns
     * @param array $where
     */
    public function update(array $columns = [], array $where = []): Orm
    {
        $this->where = $where;
        return $this->setColumns($columns)
            ->setQuery(new Update())
            ->build($this->tablename, $this->columns, $this->where);
    }

    /**
     * insert record with columns fields
     * @param array $columns
     */
    public function insert(array $columns = []): Orm
    {
        return $this
            ->setColumns($columns)
            ->setQuery(new Insert())
            ->build($this->tablename, $this->columns, []);
    }

    /**
     * delete matching where criterias
     * @param array $where
     */
    public function delete(array $where = []): Orm
    {
        $this->where = $where;
        return $this
            ->setQuery(new Delete())
            ->build($this->tablename, [], $this->where);
    }

    /**
     * get primary key
     *
     * @return string
     */
    public function getPrimary(): string
    {
        return $this->primary;
    }

    /**
     * get slot name
     *
     * @return string
     */
    public function getSlot(): string
    {
        return $this->slot;
    }

    /**
     * get service container
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * get database name
     *
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
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
     * query builder values
     * @return array
     */
    public function getBuilderValues(): array
    {
        $this->getSql();
        return $this->queryBuilder->getValues();
    }

    /**
     * set query instance
     *
     * @param Select|Update|Insert|Delete $query
     * @return Orm
     */
    public function setQuery($query): Orm
    {
        $this->query = $query;
        return $this;
    }

    /**
     * query sql string
     * @return string
     */
    public function getSql(): string
    {
        return $this->queryBuilder->write($this->query);
    }

    /**
     * build query
     *
     * @param string $tablename
     * @param array $columns
     * @param array $where
     * @return Orm
     */
    protected function build(string $tablename, array $columns, array $where): Orm
    {
        if (false === is_object($this->getQuery())) {
            throw new \Exception('Build : Invalid query instance');
        }
        $queryClassname = get_class($this->getQuery());
        $allowedClasses = [
            Update::class, Select::class, Delete::class, Insert::class
        ];
        if (false === in_array($queryClassname, $allowedClasses)) {
            throw new \Exception('Build : Invalid query type');
        }
        $this->query->setTable($tablename);
        switch ($queryClassname) {
            case Select::class:
                $this->query->setColumns($columns);
                break;
            case Update::class:
                if (empty($columns)) {
                    throw new \Exception(
                        'Build : Update requires not empty payload'
                    );
                }
                if (empty($where)) {
                    throw new \Exception(
                        'Build : Update requires at least one condition'
                    );
                }
                $this->query->setValues($columns);
                break;
            case Insert::class:
                if (empty($columns)) {
                    throw new \Exception(
                        'Build : Insert requires not empty payload'
                    );
                }
                $this->query->setValues($columns);
                break;
            case Delete::class:
                if (empty($where)) {
                    throw new \Exception(
                        'Build : Delete requires at least one condition'
                    );
                }
                break;
        }
        return $this->buildWhere($where);
    }

    /**
     * build where condition on query
     *
     * @param array $where
     * @return Orm
     */
    protected function buildWhere(array $where): Orm
    {
        if (false === empty($where)) {
            foreach ($where as $k => $v) {
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
        if ($operator == '!') {
            return ($hasArray) ? 'notIn' : 'notEquals';
        } elseif ($operator == '<') {
            return 'lessThan';
        } elseif ($operator == '>') {
            return 'greaterThan';
        } elseif ($operator == '#') {
            return 'like';
        }
    }

    /**
     * set query sort order
     *
     * @param array $orders
     * @return Orm
     */
    protected function setOrder(array $orders): Orm
    {
        $this->order = $orders;
        foreach ($this->order as $k => $v) {
            $this->query->orderBy($k, $v);
        }
        return $this;
    }
}
