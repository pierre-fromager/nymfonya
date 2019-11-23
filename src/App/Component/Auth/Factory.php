<?php

namespace App\Component\Auth;

use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;
use App\Component\Auth\Adapters\File as FileAdapter;
use App\Component\Auth\Adapters\Config as ConfigAdapter;
use App\Component\Auth\Adapters\Repository as RepositoryAdapter;

class Factory implements AdapterInterface
{

    const CONFIG_AUTH_ADAPTER = 'auth';

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * adapter
     *
     * @var AdapterInterface
     */
    protected $adapter;

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
     * set auth adapter
     *
     * @param string $classname
     * @return Factory
     */
    public function setAdapter(string $classname = ''): Factory
    {
        if (empty($classname)) {
            $config = $this->container->getService(Config::class);
            if (false === $config->hasEntry(self::CONFIG_AUTH_ADAPTER)) {
                throw new \Exception('Missing auth config entry');
            }
            $adapterConfig = $config->getSettings(self::CONFIG_AUTH_ADAPTER);
            if (false === isset($adapterConfig['adapter'])) {
                throw new \Exception('Missing auth config adapter');
            }
            $adapterClassname = $adapterConfig['adapter'];
        } else {
            $adapterClassname = $classname;
        }
        $allowedAdapters = [
            FileAdapter::class,
            ConfigAdapter::class,
            RepositoryAdapter::class
        ];
        if (false === in_array($adapterClassname, $allowedAdapters)) {
            throw new \Exception('Bad auth adapter classname');
        }
        $this->adapter = new $adapterClassname($this->container);
        return $this;
    }

    /**
     * return adapter instance
     *
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * return auth user
     *
     * @return array
     */
    public function auth(string $login, string $password): array
    {
        return $this->getAdapter()->auth($login, $password);
    }
}
