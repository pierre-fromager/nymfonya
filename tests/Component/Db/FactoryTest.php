<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use \PDO;
use App\Config;
use App\Component\Container;
use App\Component\Db\Factory;

/**
 * @covers App\Component\Db\Factory::<public>
 */
class ComponentDbFactoryTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';
    const DB_SLOT_TEST = 'test';
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
     * @var Factory
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
        $this->instance = new Factory($this->container);
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
        $class = new \ReflectionClass(Factory::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Db\Factory::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Factory);
    }

    /**
     * testGetConnection
     * @covers App\Component\Db\Factory::getConnection
     */
    public function testGetConnection()
    {
        $this->assertTrue(
            $this->instance->getConnection(
                self::DB_SLOT_TEST,
                self::DB_NAME_TEST
            ) instanceof PDO
        );
        $this->assertTrue(
            $this->instance->getConnection(
                self::DB_SLOT_TEST,
                self::DB_NAME_TEST
            ) instanceof PDO
        );
    }

    /**
     * testConnect
     * @covers App\Component\Db\Factory::connect
     */
    public function testConnect()
    {
        $conn = self::getMethod('connect')->invokeArgs(
            $this->instance,
            [self::DB_SLOT_TEST, self::DB_NAME_TEST]
        );
        $this->assertTrue($conn instanceof Factory);
    }

    /**
     * testAdapterParams
     * @covers App\Component\Db\Factory::adapterParams
     */
    public function testAdapterParams()
    {
        $adp = self::getMethod('adapterParams')->invokeArgs(
            $this->instance,
            [self::DB_SLOT_TEST, self::DB_NAME_TEST]
        );
        $this->assertTrue(is_array($adp));
        $this->assertNotEmpty($adp);
    }

    /**
     * testAdapterParamsException
     * @covers App\Component\Db\Factory::adapterParams
     */
    public function testAdapterParamsException()
    {
        $this->expectException(\Exception::class);
        self::getMethod('adapterParams')->invokeArgs(
            $this->instance,
            [self::DB_SLOT_TEST, 'badDbName']
        );
    }

    /**
     * testIdentity
     * @covers App\Component\Db\Factory::identity
     */
    public function testIdentity()
    {
        $ide = self::getMethod('identity')->invokeArgs(
            $this->instance,
            [self::DB_SLOT_TEST, self::DB_NAME_TEST]
        );
        $this->assertTrue(is_string($ide));
        $this->assertNotEmpty($ide);
    }
}
