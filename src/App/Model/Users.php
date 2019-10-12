<?php

namespace App\Model;

use App\Config;

/**
 * Users class is a basic class to let auth process running.
 * It uses fake accounts come from config accounts key.
 * For security reason, don't use this in prod /!\
 */
class Users
{

    const _ID = 'id';
    const _NAME = 'name';
    const _EMAIL = 'email';
    const _PASSWORD = 'password';
    const _ROLE = 'role';
    const _VALID = 'valid';
    const _ACCOUNTS = 'accounts';

    /**
     * app config
     *
     * @var Config
     */
    private $config;

    /**
     * accounts list
     *
     * @var array
     */
    private $accounts;

    /**
     * instanciate
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->accounts = $this->config->getSettings(self::_ACCOUNTS);
    }

    /**
     * auth a user for a given email and password
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function auth(string $email, string $password): array
    {
        $ac = count($this->accounts);
        for ($c = 0; $c < $ac; $c++) {
            $user = $this->accounts[$c];
            if ($user[self::_EMAIL] === $email
                && $password === $user[self::_PASSWORD]
            ) {
                return $user;
            }
        }
        return [];
    }

    /**
     * return user array for a given user id
     *
     * @param integer $id
     * @return array
     */
    public function getById(int $id): array
    {
        return isset($this->accounts[$id]) ? $this->accounts[$id] : [];
    }
}
