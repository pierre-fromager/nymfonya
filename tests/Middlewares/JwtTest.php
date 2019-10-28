<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use PHPUnit\Framework\MockObject\MockObject;
use App\Config;
use App\Container;
use App\Http\Middleware;
use App\Http\Interfaces\Middleware\ILayer;
use App\Http\Response;
use App\Middlewares\Jwt as JwtMiddleware;
use App\Tools\Jwt\Token;

/**
 * @covers \App\Middlewares\Jwt::<public>
 */
class AppMiddlewaresJwtTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';


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
     * middleware layer
     *
     * @var ILayer
     */
    protected $layer;

    /**
     * reflector instance on layer
     *
     * @var ReflectionObject
     */
    protected $layerReflector;

    /**
     * $tokenTool
     *
     * @var Token
     */
    protected $tokenTool;

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
        $this->init();
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
        $this->layerReflector = null;
        $this->tokenTool = null;
    }

    /**
     * return invocated method result for given
     * layer instance,
     * method name,
     * array of params.
     *
     * @param ILayer $layer
     * @param string $name
     * @param array $params
     * @return void
     */
    protected function invokeMethod(
        ILayer $layer,
        string $name,
        array $params = []
    ) {
        $met = $this->layerReflector->getMethod($name);
        $met->setAccessible(true);
        return $met->invokeArgs($layer, $params);
    }

    /**
     * returns mocked request following success param
     * when success is true valid credentials params get setted valid
     * for login and password or invalid credentials provided.
     *
     * @return MockObject
     */
    protected function getMockedRequest(bool $withProcess, array $user): MockObject
    {
        $uri = ($withProcess)
            ? '/api/v1/stat/filecache'
            : '/api/v1/auth/login';
        $userPayload = ($user) ? $user : $this->getUser($withProcess);
        $mockRequest = $this->createMock(\App\Http\Request::class);
        $mockRequest->method('getUri')->willReturn($uri);
        $mockRequest->method('isCli')->willReturn(true);
        $mockRequest->method('getHeaders')->willReturn(
            [
                JwtMiddleware::_AUTORIZATION => 'Bearer '
                    . $this->getToken($userPayload)
            ]
        );
        return $mockRequest;
    }

    /**
     * returns mocked request for a given uri
     * no additionals headers provided.
     *
     * @return MockObject
     */
    protected function getMockedRequestUri(string $uri): MockObject
    {
        $mockRequest = $this->createMock(\App\Http\Request::class);
        $mockRequest->method('getUri')->willReturn($uri);
        $mockRequest->method('isCli')->willReturn(true);
        return $mockRequest;
    }

    /**
     * init test with or without request mocked
     *
     * @param boolean $withMock
     * @param boolean $withProcess
     * @return void
     */
    protected function init(bool $withMock = false, bool $withProcess = false, $user = [])
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
                $this->getMockedRequest($withProcess, $user)
            );
        }
        $this->tokenTool = new Token(
            $this->config,
            $this->container->getService(\App\Http\Request::class)
        );
        $this->layer = new JwtMiddleware();
        $this->instance = new Middleware();
        $this->layerReflector = new \ReflectionObject($this->layer);
    }

    /**
     * return a token user
     *
     * @param array $user
     * @return string
     */
    protected function getToken(array $user): string
    {
        $this->tokenTool
            ->setIssueAt(time())
            ->setIssueAtDelay(-100)
            ->setTtl(1200);
        return $this->tokenTool->encode(
            $user['id'],
            $user['email'],
            $user['password']
        );
    }

    /**
     * returns a user with validity
     *
     * @return array
     */
    protected function getUser(bool $valid = true, int $uid = 0): array
    {
        $accounts = $this->config->getSettings(Config::_ACCOUNTS);
        $user = $accounts[0];
        $user['id'] = ($uid === 0) ? $user['id'] : 200;
        $user['email'] = ($valid) ? $user['email'] : 'bademail@domain.tld';
        $user['login'] = $user['email'];
        unset($accounts);
        return $user;
    }

    /**
     * run the peel layer middleware process
     * this init from the Container as kernel use to when execute
     *
     * @return Container
     */
    protected function peelLayer(): Container
    {
        $dummyCoreController = function ($v) {
            return $v;
        };
        return $this->instance->layer($this->layer)->peel(
            $this->container,
            $dummyCoreController
        );
    }

    /**
     * testInstance
     * @covers App\Http\Middleware::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Middleware);
    }

    /**
     * testPeel
     * @covers App\Middlewares\Jwt::peel
     */
    public function testPeel()
    {
        $this->assertTrue($this->peelLayer() instanceof Container);
    }

    /**
     * testInit
     * @covers App\Middlewares\Jwt::init
     */
    public function testInit()
    {
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'init', [$this->container]);
        $this->assertTrue($peelReturn instanceof Container);
        unset($peelReturn);
    }

    /**
     * testProcessSuccess
     * @covers App\Middlewares\Jwt::setEnabled
     * @covers App\Middlewares\Jwt::process
     */
    public function testProcessSuccess()
    {
        $this->setOutputCallback(function () {
        });
        $this->init(true, true);
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testProcessFailedBadCredential
     * @covers App\Middlewares\Jwt::setEnabled
     * @covers App\Middlewares\Jwt::process
     */
    public function testProcessFailedBadCredential()
    {
        $this->setOutputCallback(function () {
        });
        $this->init(true, true, $this->getUser(false));
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
        $res = $peelReturn->getService(\App\Http\Response::class);
        $this->assertEquals($res->getCode(), 403);
        $this->assertEquals(
            $res->getContent(),
            '{"error":true,"errorMessage":"Auth failed : bad credentials"}'
        );
    }

    /**
     * testProcessFailedBadUser
     * @covers App\Middlewares\Jwt::setEnabled
     * @covers App\Middlewares\Jwt::process
     */
    public function testProcessFailedBadUser()
    {
        $this->setOutputCallback(function () {
        });
        $this->init(true, true, $this->getUser(false, 200));
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
        $res = $peelReturn->getService(\App\Http\Response::class);
        $this->assertEquals($res->getCode(), 403);
        $this->assertEquals(
            $res->getContent(),
            '{"error":true,"errorMessage":"Auth failed : bad user"}'
        );
    }

    /**
     * testProcessFailedUnauthorized
     * @covers App\Middlewares\Jwt::setEnabled
     * @covers App\Middlewares\Jwt::process
     */
    public function testProcessFailedUnauthorized()
    {
        $this->setOutputCallback(function () {
        });
        $this->init(true, true, $this->getUser(false, 200));
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
        $res = $peelReturn->getService(\App\Http\Response::class);
        $this->assertEquals($res->getCode(), 403);
        $this->assertEquals(
            $res->getContent(),
            '{"error":true,"errorMessage":"Auth failed : bad user"}'
        );
    }

    /**
     * testProcessMissingToken
     * @covers App\Middlewares\Jwt::setEnabled
     * @covers App\Middlewares\Jwt::process
     */
    public function testProcessMissingToken()
    {
        $this->setOutputCallback(function () {
        });
        $this->container->setService(
            \App\Http\Request::class,
            $this->getMockedRequestUri('/api/v1/stat/filecache')
        );
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
        $res = $peelReturn->getService(\App\Http\Response::class);
        $this->assertEquals($res->getCode(), 403);
        $this->assertEquals(
            $res->getContent(),
            '{"error":true,"errorMessage":"Auth failed : Token required"}'
        );
    }

    /**
     * testIsPreflight
     * @covers App\Middlewares\Jwt::isPreflight
     */
    public function testIsPreflight()
    {
        $peelReturn = $this->peelLayer();
        $ip = $this->invokeMethod($this->layer, 'isPreflight', []);
        $this->assertTrue(is_bool($ip));
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testIsValidCredential
     * @covers App\Middlewares\Jwt::isValidCredential
     */
    public function testIsValidCredential()
    {
        $peelReturn = $this->peelLayer();
        $validUser = $this->getUser();
        $tokenGen = $this->getToken($validUser);
        $decodedToken = $this->tokenTool->decode($tokenGen);
        $ivc = $this->invokeMethod(
            $this->layer,
            'isValidCredential',
            [$decodedToken, $validUser]
        );
        $this->assertTrue(is_bool($ivc));
        $this->assertTrue($ivc);
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testGetUser
     * @covers App\Middlewares\Jwt::getUser
     */
    public function testGetUser()
    {
        $peelReturn = $this->peelLayer();
        $gus = $this->invokeMethod(
            $this->layer,
            'getUser',
            [0]
        );
        $this->assertTrue(is_array($gus));
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testIsValidAuthorization
     * @covers App\Middlewares\Jwt::isValidAuthorization
     */
    public function testIsValidAuthorization()
    {
        $peelReturn = $this->peelLayer();
        $iva = $this->invokeMethod(
            $this->layer,
            'isValidAuthorization',
            []
        );
        $this->assertTrue(is_bool($iva));
        $this->assertFalse($iva);
        $this->assertTrue($peelReturn instanceof Container);
        $this->init(true, true);
        $peelReturn = $this->peelLayer();
        $iva = $this->invokeMethod(
            $this->layer,
            'isValidAuthorization',
            []
        );
        $this->assertTrue(is_bool($iva));
        $this->assertTrue($iva);
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testRequired
     * @covers App\Middlewares\Jwt::required
     */
    public function testRequired()
    {
        $peelReturn = $this->peelLayer();
        $requ = $this->invokeMethod($this->layer, 'required', []);
        $this->assertTrue(is_bool($requ));
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testIsExclude
     * @covers App\Middlewares\Jwt::isExclude
     */
    public function testIsExclude()
    {
        $peelReturn = $this->peelLayer();
        $ie = $this->invokeMethod($this->layer, 'isExclude', []);
        $this->assertTrue(is_bool($ie));
        $this->assertTrue($peelReturn instanceof Container);

        $this->init(true, false);
        $peelReturn = $this->peelLayer();
        $ie = $this->invokeMethod($this->layer, 'isExclude', []);
        $this->assertTrue(is_bool($ie));
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testRequestUriPrefix
     * @covers App\Middlewares\Jwt::requestUriPrefix
     */
    public function testRequestUriPrefix()
    {
        $peelReturn = $this->peelLayer();
        $rup = $this->invokeMethod($this->layer, 'requestUriPrefix', []);
        $this->assertTrue(is_string($rup));
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testSendError
     * @covers App\Middlewares\Jwt::sendError
     */
    public function testSendError()
    {
        $this->setOutputCallback(function () {
        });
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'sendError', [
            500, 'error message'
        ]);
        $this->assertTrue($peelReturn instanceof Container);
        $res = $peelReturn->getService(\App\Http\Response::class);
        $this->assertTrue($res instanceof Response);
        $this->assertEquals($res->getCode(), 500);
        $this->assertEquals(
            $res->getContent(),
            '{"error":true,"errorMessage":"Auth failed : error message"}'
        );
    }
}
