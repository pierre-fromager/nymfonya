<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Http\Request;
use App\Config;
use App\Component\Container;
use App\Component\Http\Middleware;
use App\Component\Http\Interfaces\Middleware\ILayer;
use App\Middlewares\Restful;

/**
 * @covers \App\Middlewares\Restful::<public>
 */
class AppMiddlewaresRestfulTest extends PFT
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
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $kernel = new \App\Kernel(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container->setService(\App\Kernel::class, $kernel);
        $this->layer = new Restful();
        $this->instance = new Middleware();
        $this->layerReflector = new \ReflectionObject($this->layer);
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
     * @covers App\Component\Http\Middleware::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Middleware);
    }

    /**
     * testPeel
     * @covers App\Middlewares\Restful::peel
     */
    public function testPeel()
    {
        $this->assertTrue($this->peelLayer() instanceof Container);
    }

    /**
     * testInit
     * @covers App\Middlewares\Restful::init
     */
    public function testInit()
    {
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'init', [$this->container]);
        $this->assertTrue($peelReturn instanceof Container);
        unset($rl);
    }

    /**
     * testProcess
     * @covers App\Middlewares\Restful::setEnabled
     * @covers App\Middlewares\Restful::process
     */
    public function testProcess()
    {
        $fakeUri = '/api/v1/restful?id=2';
        $mockRequest = $this->createMock(\App\Component\Http\Request::class);
        $mockRequest->method('getUri')->willReturn($fakeUri);
        $mockRequest->method('getMethod')->willReturn(Request::METHOD_POST);
        $this->container->setService(\App\Component\Http\Request::class, $mockRequest);
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testCaUri
     * @covers App\Middlewares\Restful::caUri
     */
    public function testCaUri()
    {
        $fakeUri = '/api/v1/restful?id=2';
        $mockRequest = $this->createMock(\App\Component\Http\Request::class);
        $mockRequest->method('getUri')->willReturn($fakeUri);
        $mockRequest->method('getMethod')->willReturn(Request::METHOD_POST);
        $this->container->setService(\App\Component\Http\Request::class, $mockRequest);
        $peelReturn = $this->peelLayer();
        $cau = $this->invokeMethod($this->layer, 'caUri', []);
        $this->assertNotEmpty($cau);
        $this->assertEquals($cau, 'restful?id=2');
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testRequired
     * @covers App\Middlewares\Restful::required
     */
    public function testRequired()
    {
        $fakeUri = '/api/v1/restful?id=2';
        $mockRequest = $this->createMock(\App\Component\Http\Request::class);
        $mockRequest->method('getUri')->willReturn($fakeUri);
        $mockRequest->method('getMethod')->willReturn(Request::METHOD_POST);
        $this->container->setService(\App\Component\Http\Request::class, $mockRequest);
        $peelReturn = $this->peelLayer();
        $this->assertTrue($peelReturn instanceof Container);
        $requ0 = $this->invokeMethod($this->layer, 'required', []);
        $this->assertTrue(is_bool($requ0));
        $this->assertTrue($requ0);
    }

    /**
     * testIsExclude
     * @covers App\Middlewares\Restful::isExclude
     */
    public function testIsExclude()
    {
        $fakeUriOk = '/api/v1/restful?id=2';
        $mockRequest = $this->createMock(\App\Component\Http\Request::class);
        $mockRequest->method('getUri')->willReturn($fakeUriOk);
        $mockRequest->method('getMethod')->willReturn(Request::METHOD_POST);
        $this->container->setService(\App\Component\Http\Request::class, $mockRequest);
        $peelReturn = $this->peelLayer();
        $this->assertTrue($peelReturn instanceof Container);
        $requ0 = $this->invokeMethod($this->layer, 'isExclude', []);
        $this->assertTrue(is_bool($requ0));
        $this->assertFalse($requ0);
        unset($mockRequest);
        $fakeUriNok = '/api/v1/test/jwtaction';
        $mockRequest = $this->createMock(\App\Component\Http\Request::class);
        $mockRequest->method('getMethod')->willReturn(Request::METHOD_GET);
        $mockRequest->method('getUri')->willReturn($fakeUriNok);
        $this->container->setService(\App\Component\Http\Request::class, $mockRequest);
        $peelReturn = $this->peelLayer();
        $this->assertTrue($peelReturn instanceof Container);
        $requ0 = $this->invokeMethod($this->layer, 'isExclude', []);
        $this->assertTrue(is_bool($requ0));
        $this->assertTrue($requ0);
        unset($mockRequest);
    }

    /**
     * testRequestUriPrefix
     * @covers App\Middlewares\Restful::requestUriPrefix
     */
    public function testRequestUriPrefix()
    {
        $peelReturn = $this->peelLayer();
        $rup = $this->invokeMethod($this->layer, 'requestUriPrefix', []);
        $this->assertTrue(is_string($rup));
        $this->assertTrue($peelReturn instanceof Container);
    }
}
