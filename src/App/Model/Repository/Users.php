<?php

namespace App\Model\Repository;

use App\Component\Container;
use App\Component\Model\Orm\Orm;

class Users extends Orm
{

    /**
     * table name
     * @var string
     */
    protected $tablename = 'users';

    /**
     * table primary key
     * @var string
     */
    protected $primary = 'id';

    /**
     * table name
     * @var string
     */
    protected $dbname = 'test';

    /**
     * pool name
     * @var string
     */
    protected $poolname = 'testMysql';

    /**
     * instanciate
     *
     * @param Container $container
     * @return self
     */
    public function _construct(Container $container)
    {
        parent::__construct($container);
    }
}
