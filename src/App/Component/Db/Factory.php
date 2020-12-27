<?php

declare(strict_types=1);

namespace App\Component\Db;

use PDO;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Db\Pool;
use Monolog\Logger;

class Factory
{
    const _ADAPTER = 'adapter';
    const LOGGER_PREFIX = 'Db Factory Pool : ';

    /**
     * service container
     *
     * @var Container
     */
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
     * @var Pool
     */
    private $pool;

    /**
     * logger
     *
     * @var Logger
     */
    private $logger;

    /**
     * instanciate factory
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $configService = $this->container->getService(Config::class);
        $this->config = $configService->getSettings(Config::_DB);
        $this->logger = $this->container->getService(Logger::class);
        $this->pool = $this->container->getService(Pool::class);
        $this->logger->info(self::LOGGER_PREFIX . 'instance ');
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
        if (isset($this->pool[$id])) {
            return $this->pool[$id];
        }
        $this->connect($slot, $dbname);
        return $this->pool[$id];
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
        $params = $this->adapterParams($slot, $dbname);
        try {
            $adapterClass = $params[self::_ADAPTER];
            $adapter = new $adapterClass($dbname, $params);
            $adapter->connect();
            $id = $this->identity($slot, $dbname);
            $this->pool[$id] = $adapter->getConnection();
        } catch (\PDOException $e) {
            $exMsg = self::LOGGER_PREFIX .  ' connection failed';
            $this->logger->warn($exMsg);
            throw new \Exception($exMsg, $e->getCode());
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
    protected function adapterParams(string $slot, string $dbname): array
    {
        if (false === isset($this->config[$slot][$dbname])) {
            $exMsg = sprintf(
                'Missing or invalid config on slot %s for db %s',
                $slot,
                $dbname
            );
            $this->logger->warn($exMsg);
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

    /**
     * return pool service
     *
     * @return Pool
     */
    protected function getPool(): Pool
    {
        return $this->pool;
    }
}
