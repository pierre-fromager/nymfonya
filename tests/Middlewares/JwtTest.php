<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;
use App\Http\Middleware;
use App\Http\Interfaces\Middleware\ILayer;
use App\Middlewares\Jwt as JwtMiddleware;

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
        $this->layer = new JwtMiddleware();
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
        unset($rl);
    }

    /**
     * testIsPreflight
     * @covers App\Middlewares\Jwt::setEnabled
     * @covers App\Middlewares\Jwt::process
     */
    public function testProcess()
    {
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'setEnabled', [true]);
        $this->invokeMethod($this->layer, 'process', []);
        $this->assertTrue($peelReturn instanceof Container);
        unset($rl);
    }

    /**
     * testIsPreflight
     * @covers App\Middlewares\Jwt::isPreflight
     */
    public function testIsPreflight()
    {
        $peelReturn = $this->peelLayer();
        $this->invokeMethod($this->layer, 'isPreflight', []);
        $this->assertTrue($peelReturn instanceof Container);
    }
}
