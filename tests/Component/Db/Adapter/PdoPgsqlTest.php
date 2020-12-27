<?php

namespace Tests\Component\Db\Adapter;

use PHPUnit\Framework\TestCase as PFT;
use PDO;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Db\Adapter\PdoPgsql;

/**
 * @covers App\Component\Db\Adapter\PdoPgsql::<public>
 */
class PdoPgsqlTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../../config/';
    const DB_SLOT_TEST = 'testpgsql';
    const DB_NAME_TEST = 'nymfonya';

    /**
     * config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * container instance
     *
     * @var Container
     */
    protected $container;

    /**
     * instance
     *
     * @var PdoMysql
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $adapterConfig = $this->config->getSettings(Config::_DB);
        $this->instance = new PdoPgsql(
            self::DB_NAME_TEST,
            $adapterConfig[self::DB_SLOT_TEST][self::DB_NAME_TEST]
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->config = null;
        $this->container = null;
        $this->instance = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(PdoPgsql::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Db\Adapter\PdoPgsql::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof PdoPgsql);
    }

    /**
     * testConnect
     * @covers App\Component\Db\Adapter\PdoPgsql::connect
     */
    public function testConnect()
    {
        $this->assertTrue(
            $this->instance->connect() instanceof PdoPgsql
        );
    }

    /**
     * testGetConnection
     * @covers App\Component\Db\Adapter\PdoPgsql::connect
     * @covers App\Component\Db\Adapter\PdoPgsql::getConnection
     */
    public function testGetConnection()
    {
        $this->assertTrue(
            $this->instance->connect() instanceof PdoPgsql
        );
        $this->assertTrue(
            $this->instance->getConnection() instanceof PDO
        );
    }

    /**
     * testGetConnection
     * @covers App\Component\Db\Adapter\PdoPgsql::dsn
     */
    public function testDsn()
    {
        $dsn = self::getMethod('dsn')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($dsn));
        $this->assertNotEmpty($dsn);
    }
}
