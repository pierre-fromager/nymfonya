<?php

namespace App\Component\Auth;

interface AuthInterface
{

    /**
     * auth
     *
     * @param string $login
     * @param string $password
     * @return array
     */
    public function auth(string $login, string $password): array;
}
