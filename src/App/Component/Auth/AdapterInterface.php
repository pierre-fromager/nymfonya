<?php

namespace App\Component\Auth;

use Nymfonya\Component\Container;

interface AdapterInterface
{

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container);

    /**
     * auth
     *
     * @param string $login
     * @param string $password
     * @return array
     */
    public function auth(string $login, string $password): array;
}
