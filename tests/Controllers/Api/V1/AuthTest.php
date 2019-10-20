<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use PHPUnit\Framework\MockObject\MockObject;
use App\Config;
use App\Container;
use App\Http\Response;
use App\Controllers\Api\V1\Auth as ApiAuthControler;

/**
 * @covers \App\Controllers\Api\V1\Auth::<public>
 */
class ApiV1ControllerAuthTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../../config/';
    const _LOGIN = 'login';
    const VALID_LOGIN = 'admin@domain.tld';
    const VALID_PASSWORD = 'adminadmin';

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
        $this->init();
    }

    /**
     * init setup with or without mocked request
     *
     * @param boolean $withMock
     * @param boolean $success
     * @return void
     */
    protected function init(bool $withMock = false, bool $success = false)
    {
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        if ($withMock) {
            $this->container->setService(
                \App\Http\Request::class,
                $this->getMockedRequest($success)
            );
        }
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
     * returns mocked request
     *
     * @return MockObject
     */
    protected function getMockedRequest(bool $success): MockObject
    {

        $mockRequest = $this->createMock('App\Http\Request');
        $mockRequest->method('getMethod')->willReturn('TRACE');
        $mockRequest->method('getParam')->will(
            $this->returnCallback(
                function ($value) use ($success) {
                    if (!$success) {
                        return ($value == self::_LOGIN)
                            ? 'badlogin'
                            : 'baddpassword';
                    }
                    return ($value == self::_LOGIN)
                        ? self::VALID_LOGIN
                        : self::VALID_PASSWORD;
                }
            )
        );
        return $mockRequest;
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
     * testLoginSuccess
     * 200 means login and password match auth
     * @covers App\Controllers\Api\V1\Auth::login
     */
    public function testLoginSuccess()
    {
        $this->init(true, true);
        $this->assertTrue(
            $this->instance->login() instanceof ApiAuthControler
        );
        $res = self::getMethod('getService')->invokeArgs(
            $this->instance,
            [\App\Http\Response::class]
        );
        $this->assertEquals(
            $res->getCode(),
            Response::HTTP_OK
        );
    }

    /**
     * testLoginFailed
     * 403 means login or password don't match auth
     * @covers App\Controllers\Api\V1\Auth::login
     */
    public function testLoginFailed()
    {
        $this->init(true, false);
        $this->assertTrue(
            $this->instance->login() instanceof ApiAuthControler
        );
        $res = self::getMethod('getService')->invokeArgs(
            $this->instance,
            [\App\Http\Response::class]
        );
        $this->assertEquals(
            $res->getCode(),
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * testLoginBadRequest
     * 400 (missing required params login && password)
     * @covers App\Controllers\Api\V1\Auth::login
     */
    public function testLoginBadRequest()
    {
        $this->assertTrue(
            $this->instance->login() instanceof ApiAuthControler
        );
        $res = self::getMethod('getService')->invokeArgs(
            $this->instance,
            [\App\Http\Response::class]
        );
        $this->assertEquals(
            $res->getCode(),
            Response::HTTP_BAD_REQUEST
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
        $this->assertTrue($ivl);
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
        $this->assertTrue($ivl);
    }
}
