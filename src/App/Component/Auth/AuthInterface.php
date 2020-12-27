<?php

declare(strict_types=1);

namespace App\Component\Auth;

interface AuthInterface
{
    const _ID = 'id';
    const _EMAIL = 'email';
    const _PASSWORD = 'password';

    /**
     * auth
     *
     * @param string $login
     * @param string $password
     * @return array
     */
    public function auth(string $login, string $password): array;

    /**
     * getById
     *
     * @param integer $id
     * @return array
     */
    public function getById(int $id): array;
}
