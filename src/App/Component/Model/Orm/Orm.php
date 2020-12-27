<?php

declare(strict_types=1);

namespace App\Component\Model\Orm;

use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use App\Component\Model\Orm\InvalidQueryException;
use App\Component\Model\Orm\InvalidQueryUpdateException;
use App\Component\Model\Orm\InvalidQueryInsertException;
use App\Component\Model\Orm\InvalidQueryDeleteException;

/**
 * This is a poor man Orm
 *
 * @author Pierre Fromager <pf@pier_infor.fr>
 * @version 1.0
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
     * reset query builder
     *
     * @return void
     */
    public function resetBuilder(): Orm
    {
        $this->queryBuilder = new GenericBuilder();
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
        $this->query->setTable($this->tablename);
        if (empty($aliases)) {
            $this->query->count();
        } else {
            reset($aliases);
            $firstKey = key($aliases);
            $aliasValue = $aliases[$firstKey];
            $this->query->count($firstKey, $aliasValue);
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
     * get table name
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->tablename;
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
     * @return Select|Update|Insert|Delete
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
    public function getSql(bool $reset = true): string
    {
        return $this->queryBuilder->write($this->query, $reset);
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
            throw new InvalidQueryException(
                InvalidQueryException::MSG_INSTANCE
            );
        }
        $queryClassname = get_class($this->getQuery());
        $allowedClasses = [
            Update::class, Select::class, Delete::class, Insert::class
        ];
        if (false === in_array($queryClassname, $allowedClasses)) {
            throw new InvalidQueryException(
                InvalidQueryException::MSG_TYPE
            );
        }
        $this->query->setTable($tablename);
        switch ($queryClassname) {
            case Select::class:
                $this->query->setColumns($columns);
                break;
            case Update::class:
                if (empty($columns)) {
                    throw new InvalidQueryUpdateException(
                        InvalidQueryUpdateException::MSG_PAYLOAD
                    );
                }
                if (empty($where)) {
                    throw new InvalidQueryUpdateException(
                        InvalidQueryUpdateException::MSG_CONDITION
                    );
                }
                $this->query->setValues($columns);
                break;
            case Insert::class:
                if (empty($columns)) {
                    throw new InvalidQueryInsertException(
                        InvalidQueryInsertException::MSG_PAYLOAD
                    );
                }
                $this->query->setValues($columns);
                break;
            case Delete::class:
                if (empty($where)) {
                    throw new InvalidQueryDeleteException(
                        InvalidQueryDeleteException::MSG_CONDITION
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
                if (!is_string($k)) {
                    throw new \Exception(
                        'Build : Where condition invalid key'
                    );
                }
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
     * @param mixed $value
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
