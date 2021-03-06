<?php

namespace Tests\Controllers\Api\V1;

use PHPUnit\Framework\TestCase as PFT;
use PHPUnit\Framework\MockObject\MockObject;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use App\Controllers\Api\V1\Auth as ApiAuthControler;
use Tests\Fake\Credential;

/**
 * ApiV1ControllerAuthTest
 *
 * Tests a single login final public action
 * and associated protected methods.
 * This illustrates how to mock an object and update Container
 * with mocked object (Request).
 * Response is tested to match code requirements.
 *
 * @covers \App\Controllers\Api\V1\Auth::<public>
 */
class AuthTest extends PFT
{
    use Credential;

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../../config/';
    const _LOGIN = 'login';

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
    protected function setUp(): void
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->init();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
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
     * init setup with or without mocked request
     * success when true set valid credentials on request params
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
                Request::class,
                $this->getMockedRequest($success)
            );
        }
        $this->instance = new ApiAuthControler($this->container);
    }

    /**
     * returns mocked request following success param
     * when success is true valid credentials params get setted valid
     * for login and password or invalid credentials provided.
     *
     * @return MockObject
     */
    protected function getMockedRequest(bool $success): MockObject
    {
        $credentialValues = function ($arg0) use ($success) {
            if (!$success) {
                return ($arg0 == self::_LOGIN)
                    ? $this->loginKo()
                    : $this->passwordKo();
            }
            return ($arg0 == self::_LOGIN)
                ? $this->loginOk()
                : $this->passwordOk();
        };
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getMethod')->willReturn(
            Request::METHOD_TRACE
        );
        $mockRequest->method('getParam')->will(
            $this->returnCallback($credentialValues)
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
            [Response::class]
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
            [Response::class]
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
            [Response::class]
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
            [Response::HTTP_NOT_FOUND, 'not found']
        );
        $this->assertTrue($ser instanceof ApiAuthControler);
        $res = self::getMethod('getService')->invokeArgs(
            $this->instance,
            [Response::class]
        );
        $this->assertEquals(
            $res->getCode(),
            Response::HTTP_NOT_FOUND
        );
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
