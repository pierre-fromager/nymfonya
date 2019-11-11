<?php

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

namespace App\Component\Model\Orm;

abstract class Orm implements IOrm
{

    /**
     * config
     *
     * @var \App\Config
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
     * sql query
     * @var string
     */
    protected $query;

    /**
     * sql querybuilder method factory
     * @var string
     */
    protected $method;

    /**
     * query builder instance
     * @var GenericBuilder
     * @see https://github.com/nilportugues/php-sql-query-builder
     */
    protected $querybuilder;

    /**
     * instanciate
     * @param Container $container
     */
    public function __construct(\App\Container $container)
    {
        $this->config = $container->getService(Config::class);
        $this->querybuilder = new GenericBuilder();
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
        $this->method = self::SQL_STATEMENTS_SELECT;
        return $this;
    }

    /**
     * count records matching where criterias
     * @param array $where
     */
    public function count(array $where = []): Orm
    {
        $this->where = $where;
        $this->method = self::SQL_STATEMENTS_SELECT;
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
        $this->method =  self::SQL_STATEMENTS_UPDATE;
        return $this;
    }

    /**
     * insert record with what fields
     * @param array $what
     */
    public function insert(array $what = []): Orm
    {
        $this->what = $what;
        $this->method = self::SQL_STATEMENTS_INSERT;
        return $this;
    }

    /**
     * delete matching where criterias
     * @param array $where
     */
    public function delete(array $where = []): Orm
    {
        $this->where = $where;
        $this->method = self::SQL_STATEMENTS_DELETE;
        return $this;
    }

    /**
     * returns sql builder instance
     * @return GenericBuilder
     */
    protected function getQueryBuilder(): GenericBuilder
    {
        return $this->querybuilder;
    }

    /**
     * build sql query
     *
     * @return Orm
     */
    protected function build(): Orm
    {
        switch ($this->method) {
            case self::SQL_STATEMENTS_SELECT:
                $this->query = $this->querybuilder
                    ->select()
                    ->setTable($this->tablename)
                    ->setColumns($this->what);
                break;
            case self::SQL_STATEMENTS_UPDATE:
                $this->query = $this->querybuilder
                    ->update()
                    ->setTable($this->tablename)
                    ->setValues($this->what);
                break;

            default:
                break;
        }
        return $this;
    }

    /**
     * return sql built query
     *
     * @return string
     */
    protected function getQuey():string
    {
        return $this->query;
    }
}
