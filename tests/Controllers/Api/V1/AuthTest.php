<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;
use App\Controllers\Api\V1\Auth as ApiAuthControler;

/**
 * @covers \App\Controllers\Api\V1\Auth::<public>
 */
class ApiV1ControllerAuthTest extends PFT
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
     * @var ApiAuthControler
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
        $this->instance = new ApiAuthControler($this->container);
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
        $class = new \ReflectionClass(ApiAuthControler::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Controllers\Api\V1\Auth::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof ApiAuthControler);
    }

    /**
     * testLoginAction
     * @covers App\Controllers\Api\V1\Auth::login
     */
    public function testLoginAction()
    {
        $this->assertTrue(
            $this->instance->login() instanceof ApiAuthControler
        );
    }

    /**
     * testSetErrorResponse
     * @covers App\Controllers\Api\V1\Auth::setErrorResponse
     */
    public function testSetErrorResponse()
    {
        $ser = self::getMethod('setErrorResponse')->invokeArgs(
            $this->instance,
            [404, 'not found']
        );
        $this->assertTrue($ser instanceof ApiAuthControler);
    }

    /**
     * testIsValidLogin
     * @covers App\Controllers\Api\V1\Auth::isValidLogin
     */
    public function testIsValidLogin()
    {
        $ivl = self::getMethod('isValidLogin')->invokeArgs(
            $this->instance,
            ['login', 'password']
        );
        $this->assertTrue(is_bool($ivl));
    }

    /**
     * testIsLoginMethodAllowed
     * @covers App\Controllers\Api\V1\Auth::isLoginMethodAllowed
     */
    public function testIsLoginMethodAllowed()
    {
        $ivl = self::getMethod('isLoginMethodAllowed')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($ivl));
    }
}
