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
     */
    public function testSend()
    {
        $kr = $this->instance->run();
        $this->assertTrue($kr instanceof Kernel);
        $ks = $kr->send();
        $this->assertTrue($ks instanceof Kernel);
    }

    /**
     * testGetInstance
     * @covers App\Kernel::getInstance
     */
    public function testGetInstance()
    {
        $kgi = $this->instance->getInstance();
        $this->assertTrue($kgi instanceof Kernel);
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
}
