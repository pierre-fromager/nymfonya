<?php

namespace App\Component\Auth\Adapters;

use Nymfonya\Component\Config as AppConfig;
use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;
use App\Model\Users;

class Config implements AdapterInterface
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
        $config = $this->container->getService(AppConfig::class);
        return (new Users($config))->auth($login, $password);
    }
}
