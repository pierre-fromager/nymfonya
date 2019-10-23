<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;
use App\Controllers\Api\V1\Test as ApiTestControler;
use PHPUnit\Util\Test;

/**
 * @covers \App\Controllers\Api\V1\Test::<public>
 */
class ApiV1ControllerTestTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../../config/';

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
     * @var ApiTestControler
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
        $this->instance = new ApiTestControler($this->container);
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
        $class = new \ReflectionClass(Test::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Controllers\Api\V1\Test::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof ApiTestControler);
    }

    /**
     * testJwtAction
     * @covers App\Controllers\Api\V1\Test::jwtaction
     */
    public function testJwtAction()
    {
        $this->assertTrue(
            $this->instance->jwtaction() instanceof ApiTestControler
        );
    }
}
