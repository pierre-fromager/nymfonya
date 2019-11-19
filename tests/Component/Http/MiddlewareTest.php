<?php

namespace Tests\Component\Http;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Http\Kernel;
use App\Component\Config;
use App\Component\Container;
use App\Component\Http\Middleware;

/**
 * @covers \App\Component\Http\Middleware::<public>
 */
class MiddlewareTest extends PFT
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
     * @var Middleware
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
        $serviceConfig = $this->config->getSettings(Config::_SERVICES);
        $this->container = new Container($serviceConfig);
        $kernel = new Kernel(Config::ENV_CLI, basename(self::CONFIG_PATH));
        $this->container->setService(Kernel::class, $kernel);
        $this->instance = new Middleware();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
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
        $class = new \ReflectionClass(Middleware::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Http\Middleware::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Middleware);
    }

    /**
     * testLayerException
     * @covers App\Component\Http\Middleware::layer
     */
    public function testLayerException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->instance->layer(new \stdClass());
    }

    /**
     * testLayerException
     * @covers App\Component\Http\Middleware::layer
     */
    public function testLayer()
    {
        $r = $this->instance->layer(
            new \App\Middlewares\After($this->container)
        );
        $this->assertTrue($r instanceof Middleware);
        $r = $this->instance->layer(new Middleware());
        $this->assertTrue($r instanceof Middleware);
    }

    /**
     * testPeel
     * @covers App\Component\Http\Middleware::peel
     */
    public function testPeel()
    {
        $r = $this->instance->peel(
            $this->container,
            function ($v) {
                return time();
            }
        );
        $this->assertTrue(is_int($r));
    }

    /**
     * testToArray
     * @covers App\Component\Http\Middleware::toArray
     */
    public function testToArray()
    {
        $this->assertTrue(is_array($this->instance->toArray()));
    }

    /**
     * testCreateCoreFunction
     * @covers App\Component\Http\Middleware::createCoreFunction
     */
    public function testCreateCoreFunction()
    {
        $value = self::getMethod('createCoreFunction')->invokeArgs(
            $this->instance,
            [function ($v) {
                return $v + 1;
            }]
        );
        $this->assertTrue($value instanceof \Closure);
        $this->assertTrue(is_callable($value));
        $this->assertEquals($value(1), 2);
        $invoked = $value->__invoke(1);
        $this->assertEquals(2, $invoked);
    }

    /**
     * testCreateLayer
     * @covers App\Component\Http\Middleware::createLayer
     */
    public function testCreateLayer()
    {
        $value = self::getMethod('createLayer')->invokeArgs(
            $this->instance,
            [
                function ($v) {
                    return $v;
                },
                new \App\Middlewares\After($this->container)
            ]
        );
        $this->assertTrue($value instanceof \Closure);
        $this->assertTrue(is_callable($value));
        $invoked = $value->__invoke($this->container);
        $this->assertTrue($invoked instanceof Container);
    }
}
