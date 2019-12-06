<?php

namespace App\Component\Auth\Adapters;

use Nymfonya\Component\Config as AppConfig;
use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;
use App\Component\Crypt;
use App\Model\Repository\Users;
use App\Component\Db\Core;

/**
 * Adapter Repository let auth from config accounts entries
 * Decryption required on password.
 */
class Repository implements AdapterInterface
{

    const _PASSWORD = 'password';

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * app config
     *
     * @var AppConfig
     */
    protected $config;

    /**
     * user repository
     *
     * @var Users
     */
    protected $userRepo;

    /**
     * db core instance
     *
     * @var Core
     */
    protected $dbCore;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->userRepo = new Users($this->container);
        $this->dbCore = new Core($this->container);
        $this->dbCore->fromOrm($this->userRepo);
        $this->config = $this->container->getService(AppConfig::class);
    }

    /**
     * auth process
     *
     * @return array
     */
    public function auth(string $login, string $password): array
    {
        $this->userRepo->getByEmail($login);
        $this->dbCore
            ->run(
                $this->userRepo->getSql(),
                $this->userRepo->getBuilderValues()
            )
            ->hydrate();
        $result = $this->dbCore->getRowset();
        if (empty($result)) {
            return [];
        }
        $user = $result[0];
        $clearPassword = $this->decrypt($user[self::_PASSWORD]);
        return ($password === $clearPassword) ? $user : [];
    }

    /**
     * return user by id
     *
     * @param integer $id
     * @return array
     */
    public function getById(int $id): array
    {
        $this->userRepo->getById($id);
        $this->dbCore
            ->run(
                $this->userRepo->getSql(),
                $this->userRepo->getBuilderValues()
            )
            ->hydrate();
        $result = $this->dbCore->getRowset();
        if (empty($result)) {
            return [];
        }
        $user = $result[0];
        $user[self::_PASSWORD] = $this->decrypt($user[self::_PASSWORD]);
        return $user;
    }

    /**
     * decrypt content
     *
     * @param string $content
     * @return string
     */
    protected function decrypt(string $content): string
    {
        return (new Crypt($this->config))->decrypt($content, true);
    }
}
