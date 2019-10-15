<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Kernel;

/**
 * @covers \App\Kernel::<public>
 */
class KernelTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../config/';
    const KERNEL_PATH = '/../src/';
    const KERNEL_NS = '\\App\\Controllers\\';

    /**
     * config
     *
     * @var Config
     */
    protected $config;

    /**
     * instance
     *
     * @var Kernel
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
        $this->instance = new Kernel(
            Config::ENV_CLI,
            __DIR__ . self::KERNEL_PATH
        );
        $this->instance->setNameSpace(self::KERNEL_NS);
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
        $class = new \ReflectionClass(Kernel::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Kernel::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Kernel);
    }

    /**
     * testRunOk
     * @covers App\Kernel::run
     */
    public function testRunOk()
    {
        $routerGroups = ['config', 'help'];
        $kr = $this->instance->run($routerGroups);
        $this->assertTrue($kr instanceof Kernel);
        $res = $kr->getService(\App\Http\Response::class);
        $this->assertTrue($res instanceof \App\Http\Response);
        $this->assertEquals(
            $res->getCode(),
            \App\Http\Response::HTTP_OK
        );
    }

    /**
     * testRunNok
     * @covers App\Kernel::run
     */
    public function testRunNok()
    {
        $routerGroups = ['badctrl', 'messup'];
        $kr = $this->instance->run($routerGroups);
        $this->assertTrue($kr instanceof Kernel);
        $res = $kr->getService(\App\Http\Response::class);
        $this->assertTrue($res instanceof \App\Http\Response);
        $this->assertEquals(
            $res->getCode(),
            \App\Http\Response::HTTP_NOT_FOUND
        );
    }

    /**
     * testSend
     * @covers App\Kernel::send
     * @covers App\Kernel::setError
     * @covers App\Kernel::getError
     * @runInSeparateProcess
     */
    public function testSend()
    {
        $this->setOutputCallback(function () {
        });
        $kr = $this->instance->run();
        $this->assertTrue($kr instanceof Kernel);
        $ks = $kr->send();
        $this->assertTrue($ks instanceof Kernel);
        self::getMethod('setError')->invokeArgs(
            $this->instance,
            [false]
        );
        $ge = self::getMethod('getError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ge);
        $kse = $this->instance->send();
        $this->assertTrue($kse instanceof Kernel);
    }

    /**
     * testSetNameSpace
     * @covers App\Kernel::setNameSpace
     */
    public function testSetNameSpace()
    {
        $sns = self::getMethod('setNameSpace')->invokeArgs(
            $this->instance,
            [self::KERNEL_NS]
        );
        $this->assertTrue($sns instanceof Kernel);
    }

    /**
     * testInit
     * @covers App\Kernel::init
     * @covers App\Kernel::getContainer
     */
    public function testInit()
    {
        self::getMethod('init')->invokeArgs(
            $this->instance,
            [
                Config::ENV_CLI,
                __DIR__ . self::KERNEL_PATH
            ]
        );
        $this->assertTrue(
            $this->instance->getService(\App\Http\Request::class)
                instanceof \App\Http\Request
        );
        $this->assertTrue(
            $this->instance->getService(\App\Http\Response::class)
                instanceof \App\Http\Response
        );
        $this->assertTrue(
            $this->instance->getService(\Monolog\Logger::class)
                instanceof \Monolog\Logger
        );
        $gc = self::getMethod('getContainer')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gc instanceof \App\Container);
    }

    /**
     * testSetGetContainer
     * @covers App\Kernel::setContainer
     * @covers App\Kernel::getContainer
     */
    public function testSetGetContainer()
    {
        self::getMethod('setContainer')->invokeArgs(
            $this->instance,
            []
        );
        $gc = self::getMethod('getContainer')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gc instanceof \App\Container);
    }

    /**
     * testSetGetError
     * @covers App\Kernel::setError
     * @covers App\Kernel::getError
     */
    public function testSetGetError()
    {
        $ges = self::getMethod('getError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ges);
        self::getMethod('setError')->invokeArgs(
            $this->instance,
            [false]
        );
        $ge = self::getMethod('getError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ge);
    }

    /**
     * testSetGetRequest
     * @covers App\Kernel::setRequest
     * @covers App\Kernel::getRequest
     */
    public function testSetGetRequest()
    {
        self::getMethod('setRequest')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(
            $this->instance->getService(\App\Http\Request::class)
                instanceof \App\Http\Request
        );
        $gr = self::getMethod('getRequest')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gr instanceof \App\Http\Request);
    }

    /**
     * testSetGetResponse
     * @covers App\Kernel::setResponse
     * @covers App\Kernel::getResponse
     */
    public function testSetGetResponse()
    {
        self::getMethod('setResponse')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(
            $this->instance->getService(\App\Http\Response::class)
                instanceof \App\Http\Response
        );
        $gr = self::getMethod('getResponse')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gr instanceof \App\Http\Response);
    }

    /**
     * testSetGetRouter
     * @covers App\Kernel::setRouter
     * @covers App\Kernel::getRouter
     */
    public function testSetGetRouter()
    {
        self::getMethod('setRouter')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(
            $this->instance->getService(\App\Http\Router::class)
                instanceof \App\Http\Router
        );
        $gr = self::getMethod('getRouter')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gr instanceof \App\Http\Router);
    }
}
