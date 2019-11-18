<?php

namespace App\Component\Db;

use \PDO;
use App\Component\Config;
use App\Component\Container;
use Exception;

class Factory
{
    const _ADAPTER = 'adapter';

    private $container;

    /**
     * factory config
     *
     * @var array
     */
    private $config;

    /**
     * connection pool
     *
     * @var array
     */
    private $connectionPool;

    /**
     * instanciate factory
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $configService = $this->container->getService(\App\Config::class);
        $this->config = $configService->getSettings(Config::_DB);
    }

    /**
     * return Pdo connection instance from pool
     *
     * @param string $slot
     * @param string $dbname
     * @return PDO
     */
    public function getConnection(string $slot, string $dbname): PDO
    {
        $id = $this->identity($slot, $dbname);
        if (isset($this->connectionPool[$id])) {
            return $this->connectionPool[$id];
        }
        $this->connect($slot, $dbname);
        return $this->connectionPool[$id];
    }

    /**
     * connect
     *
     * @param string $slot
     * @param string $dbname
     * @return void
     */
    protected function connect(string $slot, string $dbname): Factory
    {
        $this->connectionPool = (is_null($this->connectionPool))
            ? []
            : $this->connectionPool;
        $params = $this->adapterParams($slot, $dbname);
        try {
            $adapter = new $params[self::_ADAPTER]($dbname, $params);
            $adapter->connect();
            $id = $this->identity($slot, $dbname);
            $this->connectionPool[$id] = $adapter->getConnection();
        } catch (\Exception $e) {
            throw new Exception($e);
        }
        return $this;
    }

    /**
     * adapter params
     *
     * @param string $slot
     * @param string $dbname
     * @return array
     */
    protected function adapterParams(string $slot, string $dbname)
    {
        if (false === isset($this->config[$slot][$dbname])) {
            $exMsg = sprintf(
                'Missing or invalid config on slot %s for db %s',
                $slot,
                $dbname
            );
            throw new \Exception($exMsg);
        }
        return $this->config[$slot][$dbname];
    }

    /**
     * identify pool index
     *
     * @param string $slot
     * @param string $dbname
     * @return string
     */
    protected function identity(string $slot, string $dbname): string
    {
        return sprintf('%s-%s', $slot, $dbname);
    }
}
