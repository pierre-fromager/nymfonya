<?php

namespace App\Tools;

use App\Kernel;
use App\Model\Users;

class Auth
{

    protected $userModel;

    /**
     * instanciate
     */
    public function __construct()
    {
        $container = Kernel::getInstance()->getContainer();
        $this->userModel = new Users($container[\App\Config::class]);
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
