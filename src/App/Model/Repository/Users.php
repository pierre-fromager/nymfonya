<?php

namespace App\Model\Repository;

use Nymfonya\Component\Container;
use App\Component\Model\Orm\IOrm;
use App\Component\Model\Orm\Orm;

class Users extends Orm implements IOrm
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
     * database name
     * @var string
     */
    protected $database = 'nymfonya';

    /**
     * pool name
     * @var string
     */
    protected $slot = 'test';

    /**
     * instanciate
     *
     * @param Container $container
     * @return self
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * find a user for a given id
     *
     * @param integer $uid
     * @return Users
     */
    public function getById(int $uid): Users
    {
        $this->find(['*'], ['id' => $uid]);
        return $this;
    }

    /**
     * find a user for a given email
     *
     * @param integer $uid
     * @return Users
     */
    public function getByEmail(string $email): Users
    {
        $this->find(['*'], ['email' => $email]);
        return $this;
    }

    /**
     * auth from username and password
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function auth(string $email, string $password): Users
    {
        $where = ['email' => $email];
        $this->find(['*'], $where);
        return $this;
    }
}
