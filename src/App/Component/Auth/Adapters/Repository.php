<?php

namespace App\Component\Auth\Adapters;

use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;
use App\Model\Repository\Users;
use App\Component\Db\Core;

class Repository implements AdapterInterface
{

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
        $repo = (new Users($this->container))
            ->auth($login, $password);
        $dbc = new Core($this->container);
        $dbc
            ->fromOrm($repo)
            ->run($repo->getSql(), $repo->getBuilderValues())
            ->hydrate();
        $result = $dbc->getRowset();
        unset($repo, $dbc);
        return $result;
    }
}
