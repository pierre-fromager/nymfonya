<?php

namespace App\Component\Auth\Adapters;

use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;
use App\Component\Crypt;
use App\Model\Repository\Users;
use App\Component\Db\Core;

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
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * auth process
     *
     * @return array
     */
    public function auth(string $login, string $password): array
    {
        $repo = (new Users($this->container))->getByEmail($login);
        $dbc = new Core($this->container);
        $dbc
            ->fromOrm($repo)
            ->run($repo->getSql(), $repo->getBuilderValues())
            ->hydrate();
        $result = $dbc->getRowset();
        if (empty($result)) {
            unset($repo, $dbc, $result);
            return [];
        }
        $user = $result[0];
        $config = $this->container->getService(Config::class);
        $clearPassword = (new Crypt($config))->decrypt(
            $user[self::_PASSWORD],
            true
        );
        if ($password == $clearPassword) {
            unset($config, $repo, $dbc, $result);
            return $user;
        }
        unset($config, $repo, $dbc, $result);
        return [];
    }
}
