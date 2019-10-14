<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;

/**
 * @covers \App\Container::<public>
 */
class ContainerTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../config/';

    /**
     * instance
     *
     * @var Container
     */
    protected $instance;

    /**
     * config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->instance = new Container(
            $config->getSettings(Config::_SERVICES)
        );
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
        $class = new \ReflectionClass(Container::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Container::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Container);
    }

    /**
     * testInitReporter
     * @covers App\Container::initReporter
     * @covers App\Container::getReporter
     */
    public function testInitReporter()
    {
        $ir = self::getMethod('initReporter')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ir instanceof Container);
        $this->assertTrue(
            $this->instance->getReporter() instanceof \stdClass
        );
    }

    /**
     * testGetServices
     * @covers App\Container::getServices
     */
    public function testGetServices()
    {
        $this->assertTrue(is_array($this->instance->getServices()));
    }

    /**
     * testGetService
     * @covers App\Container::getService
     */
    public function testGetService()
    {
        $service = $this->instance->getService(\App\Http\Request::class);
        $this->assertTrue($service instanceof \App\Http\Request);
    }

    /**
     * testGetServiceException
     * @covers App\Container::getService
     */
    public function testGetServiceException()
    {
        $this->expectException(\Exception::class);
        $this->instance->getService('UnknownService');
    }

    /**
     * testConstructable
     * @covers App\Container::constructable
     */
    public function testConstructable()
    {
        $cInt = self::getMethod('constructable')->invokeArgs(
            $this->instance,
            [1]
        );
        $this->assertFalse($cInt);
        $cBool = self::getMethod('constructable')->invokeArgs(
            $this->instance,
            [true]
        );
        $this->assertFalse($cBool);
    }

    /**
     * testHasService
     * @covers App\Container::hasService
     */
    public function testHasService()
    {
        $hsReq = self::getMethod('hasService')->invokeArgs(
            $this->instance,
            [\App\Http\Request::class]
        );
        $this->assertTrue($hsReq);
        $hsMdl = self::getMethod('hasService')->invokeArgs(
            $this->instance,
            [\App\Model\Search::class]
        );
        $this->assertFalse($hsMdl);
    }

    /**
     * testLoad
     * @covers App\Container::load
     */
    public function testLoad()
    {
        $ld = self::getMethod('load')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ld instanceof Container);
    }

    /**
     * testCreate
     * @covers App\Container::create
     */
    public function testCreate()
    {
        $cr = self::getMethod('create')->invokeArgs(
            $this->instance,
            [
                \App\Config::class,
                [
                    self::CONFIG_PATH . \App\Config::ENV_CLI,
                    \App\Http\Request::class
                ]
            ]
        );
        $this->assertTrue($cr instanceof Container);
    }

    /**
     * testIsBasicType
     * @covers App\Container::isBasicType
     */
    public function testIsBasicType()
    {
        $ibtInt = self::getMethod('isBasicType')->invokeArgs(
            $this->instance,
            [1]
        );
        $this->assertTrue($ibtInt);
        $ibtBool = self::getMethod('isBasicType')->invokeArgs(
            $this->instance,
            [true]
        );
        $this->assertTrue($ibtBool);
        $ibtStr = self::getMethod('isBasicType')->invokeArgs(
            $this->instance,
            ['strstring']
        );
        $this->assertFalse($ibtStr);
    }
}
