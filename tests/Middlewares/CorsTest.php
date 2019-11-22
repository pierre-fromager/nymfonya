<?php

namespace Tests\Middlewares;

use PHPUnit\Framework\TestCase as PFT;
use PHPUnit\Framework\MockObject\MockObject;
use Nymfonya\Component\Config;
use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Kernel;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Middleware;
//use Nymfonya\Component\Http\Interfaces\Middleware\ILayer;
use Nymfonya\Component\Http\Interfaces\MiddlewareInterface;
use Nymfonya\Component\Http\Request;
use App\Middlewares\Cors as CorsMiddleware;

/**
 * @covers \App\Middlewares\Cors::<public>
 */
class CorsTest extends PFT
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
     * @var MiddlewareInterface
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
        $this->init();
    }

    protected function init(bool $withMock = false, bool $withProcess = false)
    {
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $kernel = new Kernel(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container->setService(Kernel::class, $kernel);
        if ($withMock) {
            $this->container->setService(
                Request::class,
                $this->getMockedRequest($withProcess)
            );
        }
        $this->layer = new CorsMiddleware();
        $this->instance = new Middleware();
        $this->layerReflector = new \ReflectionObject($this->layer);
    }

    /**
     * returns mocked request following success param
     * when success is true valid credentials params get setted valid
     * for login and password or invalid credentials provided.
     *
     * @return MockObject
     */
    protected function getMockedRequest(bool $withProcess): MockObject
    {
        $uri = ($withProcess)
            ? '/api/v1/stat/opcache'
            : '/api/v1/stat/filecache';
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')->willReturn($uri);
        return $mockRequest;
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
     * @param MiddlewareInterface $layer
     * @param string $name
     * @param array $params
     * @return void
     */
    protected function invokeMethod(
        MiddlewareInterface $layer,
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
     * @covers Nymfonya\Component\Http\Middleware::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Middleware);
    }

    /**
     * testPeel
     * @covers App\Middlewares\Cors::peel
     */
    public function testPeel()
    {
        $this->assertTrue($this->peelLayer() instanceof Container);
    }

    /**
     * testInit
     * @covers App\Middlewares\Cors::init
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
     * @covers App\Middlewares\Cors::setEnabled
     * @covers App\Middlewares\Cors::process
     */
    public function testProcess()
    {
        $this->init(true, true);
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
        $res = $this->container->getService(Response::class);
        $this->assertEquals($res->getCode(), Response::HTTP_NOT_FOUND);
    }

    /**
     * testProcessRequestOptionsMethod
     * @covers App\Middlewares\Cors::process
     */
    public function testProcessRequestOptionsMethod()
    {
        $fakeUri = '/api/v1/test/pokerelay';
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')->willReturn($fakeUri);
        $mockRequest->method('getMethod')->willReturn(Request::METHOD_OPTIONS);
        $this->container->setService(Request::class, $mockRequest);
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testCaUri
     * @covers App\Middlewares\Cors::caUri
     */
    public function testCaUri()
    {
        $fakeUri = '/api/v1/test/pokerelay';
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')->willReturn($fakeUri);
        $mockRequest->method('getMethod')->willReturn(Request::METHOD_OPTIONS);
        $this->container->setService(Request::class, $mockRequest);
        $peelReturn = $this->peelLayer();
        $cau = $this->invokeMethod($this->layer, 'caUri', []);
        $this->assertNotEmpty($cau);
        $this->assertEquals($cau, 'test/pokerelay');
        $this->assertTrue($peelReturn instanceof Container);
    }

    /**
     * testRequired
     * @covers App\Middlewares\Cors::required
     */
    public function testRequired()
    {
        $this->init(true, true);
        $peelReturn = $this->peelLayer();
        $this->assertTrue($peelReturn instanceof Container);
        $requ0 = $this->invokeMethod($this->layer, 'required', []);
        $this->assertTrue(is_bool($requ0));
        $this->assertTrue($requ0);
        $this->init(true, false);
        $peelReturn = $this->peelLayer();
        $this->assertTrue($peelReturn instanceof Container);
        $requ1 = $this->invokeMethod($this->layer, 'required', []);
        $this->assertTrue(is_bool($requ1));
        $this->assertFalse($requ1);
    }

    /**
     * testIsExclude
     * @covers App\Middlewares\Cors::isExclude
     */
    public function testIsExclude()
    {
        $peelReturn = $this->peelLayer();
        $this->assertTrue($peelReturn instanceof Container);
        $ie0 = $this->invokeMethod($this->layer, 'isExclude', []);
        $this->assertTrue(is_bool($ie0));
        $this->assertFalse($ie0);
        $this->init(true, false);
        $peelReturn = $this->peelLayer();
        $this->assertTrue($peelReturn instanceof Container);
        $ie1 = $this->invokeMethod($this->layer, 'isExclude', []);
        $this->assertTrue(is_bool($ie1));
        $this->assertTrue($ie1);
    }

    /**
     * testRequestUriPrefix
     * @covers App\Middlewares\Cors::requestUriPrefix
     */
    public function testRequestUriPrefix()
    {
        $peelReturn = $this->peelLayer();
        $rup = $this->invokeMethod($this->layer, 'requestUriPrefix', []);
        $this->assertTrue(is_string($rup));
        $this->assertTrue($peelReturn instanceof Container);
    }
}
