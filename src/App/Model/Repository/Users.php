<?php

namespace App\Model\Repository;

use App\Component\Model\Orm\Orm;

class Users extends Orm
{

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
     * instanciate
     *
     * @param \App\Container $container
     * @return self
     */
    public function _construct(\App\Container $container)
    {
        $this->tablename = 'users';
        $this->primary = 'id';
        $this->dbname = 'test';
        $this->poolname = 'testMysql';
        parent::__construct($container);
    }
}
