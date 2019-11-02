<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;
use App\Controllers\Api\V1\Restful as ApiRestfulControler;

/**
 * @covers \App\Controllers\Api\V1\Restful::<public>
 */
class ApiV1ControllerrestfulTest extends PFT
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
     * @var ApiStatControler
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
        $this->instance = new ApiRestfulControler($this->container);
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
        $class = new \ReflectionClass(ApiRestfulControler::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Controllers\Api\V1\Restful::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof ApiRestfulControler);
    }

    /**
     * testIndexAction
     * @covers App\Controllers\Api\V1\Restful::index
     */
    public function testIndexAction()
    {
        $this->assertTrue(
            $this->instance->index() instanceof ApiRestfulControler
        );
    }

    /**
     * testStoreAction
     * @covers App\Controllers\Api\V1\Restful::store
     */
    public function testStoreAction()
    {
        $this->assertTrue(
            $this->instance->store() instanceof ApiRestfulControler
        );
    }

    /**
     * testUpdateAction
     * @covers App\Controllers\Api\V1\Restful::update
     */
    public function testUpdateAction()
    {
        $this->assertTrue(
            $this->instance->update() instanceof ApiRestfulControler
        );
    }

    /**
     * testDeleteAction
     * @covers App\Controllers\Api\V1\Restful::delete
     */
    public function testDeleteAction()
    {
        $this->assertTrue(
            $this->instance->delete() instanceof ApiRestfulControler
        );
    }

    /**
     * testSetResponse
     * @covers App\Controllers\Api\V1\Restful::setResponse
     */
    public function testSetResponse()
    {
        $res = self::getMethod('setResponse')->invokeArgs(
            $this->instance,
            ['toto', 'prout']
        );
        $this->assertTrue($res instanceof ApiRestfulControler);
    }
}
