<?php

namespace Tests\Reuse\Controllers;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Config;
use App\Component\Container;
use App\Reuse\Controllers\AbstractApi;

/**
 * @covers \App\Reuse\Controllers\AbstractApi::<public>
 */
class AbstractApiTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';

    /**
     * config
     *
     * @var Config
     */
    protected $config;

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * instance
     *
     * @var Kernel
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
        $this->instance = new class ($this->container) extends AbstractApi
        {
            public function __construct(Container $container)
            {
                parent::__construct($container);
            }
        };
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->container = null;
        $this->config = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(AbstractApi::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Reuse\Controllers\AbstractApi::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof AbstractApi);
    }

    /**
     * testPreflightAction
     * @covers App\Reuse\Controllers\AbstractApi::preflight
     */
    public function testPreflightAction()
    {
        $this->assertTrue(
            $this->instance->preflight() instanceof AbstractApi
        );
    }

    /**
     * testGetService
     * @covers App\Reuse\Controllers\AbstractApi::getService
     */
    public function testGetService()
    {
        $gs = self::getMethod('getService')->invokeArgs(
            $this->instance,
            [\Monolog\Logger::class]
        );
        $this->assertTrue(is_object($gs));
        $this->assertTrue($gs instanceof \Monolog\Logger);
    }

    /**
     * testGetContainer
     * @covers App\Reuse\Controllers\AbstractApi::getContainer
     */
    public function testGetContainer()
    {
        $gc = self::getMethod('getContainer')->invokeArgs(
            $this->instance,
            [\Monolog\Logger::class]
        );
        $this->assertTrue(is_object($gc));
        $this->assertTrue($gc instanceof Container);
    }
}
