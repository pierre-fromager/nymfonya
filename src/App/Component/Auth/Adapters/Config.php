<?php

declare(strict_types=1);

namespace App\Component\Auth\Adapters;

use Nymfonya\Component\Config as AppConfig;
use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;
use App\Model\Users;

/**
 * Adapter Config let auth from config accounts entries
 * No decryption required on clear password.
 */
class Config implements AdapterInterface
{

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * user model
     *
     * @var Users
     */
    protected $modelUsers;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->modelUsers = new Users(
            $this->container->getService(AppConfig::class)
        );
    }

    /**
     * auth process
     *
     * @param string $login
     * @param string $password
     * @return array
     */
    public function auth(string $login, string $password): array
    {
        return $this->modelUsers->auth($login, $password);
    }

    /**
     * get user by id
     *
     * @param integer $id
     * @return array
     */
    public function getById(int $id): array
    {
        return $this->modelUsers->getById($id);
    }
}
