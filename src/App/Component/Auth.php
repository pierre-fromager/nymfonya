<?php

namespace App\Component;

use App\Container;
use App\Model\Users;

class Auth
{

    protected $userModel;

    /**
     * instanciate
     */
    public function __construct(Container $container)
    {
        $this->userModel = new Users(
            $container->getService(\App\Config::class)
        );
    }

    /**
     * auth from login password and return user as array
     *
     * @param string $login
     * @param string $password
     * @return array
     */
    public function auth(string $login, string $password): array
    {
        return $this->userModel->auth($login, $password);
    }
}