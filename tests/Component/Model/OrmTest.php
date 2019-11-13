<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;
use App\Http\Request;
use App\Component\Model\Orm\Orm;

/**
 * @covers \App\Component\Model\Orm\Orm::<public>
 */
class ComponentModelOrmTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';

    /**
     * config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * request instance
     *
     * @var Request
     */
    protected $request;


    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * instance
     *
     * @var Orm
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
        $this->request = new Request();
        $this->instance = new Orm($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->config = null;
        $this->request = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Orm::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Model\Orm\Orm::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Orm);
    }
}
