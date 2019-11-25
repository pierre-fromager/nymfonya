<?php

namespace Tests\Component\Db;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Db\Pool;

/**
 * @covers App\Component\Db\Pool::<public>
 */
class PoolTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';
    const DB_SLOT_TEST = 'test';
    const DB_NAME_TEST = 'nymfonya';

    /**
     * config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * container instance
     *
     * @var Container
     */
    protected $container;

    /**
     * instance
     *
     * @var Pool
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
     * init tests
     *
     * @return void
     */
    protected function init()
    {
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->initService();
    }

    /**
     * get instance from container service
     *
     * @return void
     */
    protected function initService()
    {
        $this->instance = $this->container->getService(Pool::class);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->config = null;
        $this->container = null;
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
        $class = new \ReflectionClass(Pool::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Pool);
        $ara = ['ArrayAccess' => 'ArrayAccess', 'Countable' => 'Countable'];
        $this->assertEquals($ara, class_implements(Pool::class));
        //$this->assertTrue(\is_countable($this->instance)); # Php >= 7.3
        $this->assertFalse(empty($this->instance));
    }

    /**
     * testOffsetGetSetUnsetCount
     *
     * @covers App\Component\Db\Pool::offsetGet
     * @covers App\Component\Db\Pool::offsetSet
     * @covers App\Component\Db\Pool::offsetUnset
     * @covers App\Component\Db\Pool::count
     */
    public function testOffsetGet()
    {
        $this->instance[] = 'pushit';
        $object = new \stdClass();
        $this->assertEquals(0, count($this->instance));
        $key = 'id';
        $this->instance[$key] = 2;
        $this->assertEquals(0, count($this->instance));
        $this->instance[20] = 2;
        $this->assertEquals(0, count($this->instance));
        $this->instance[$key] = $object;
        $this->assertEquals($object, $this->instance[$key]);
        unset($this->instance[$key]);
        $this->assertFalse(isset($this->instance[$key]));
        $this->assertEquals(0, count($this->instance));
    }

    /**
     * validProvider
     * @return Array
     */
    public function validProvider()
    {
        $object = new \stdClass();
        return [
            [null, null, false],
            [null, 10, false],
            [null, 'value', false],
            [null, $object, false],
            [false, null, false],
            [false, 10, false],
            [false, 'value', false],
            [false, $object, false],
            [true, null, false],
            [true, 10, false],
            [true, 'value', false],
            [true, $object, false],
            [10, null, false],
            [10, 10, false],
            [10, 'value', false],
            [10, $object, false],
            ['key', 'value', false],
            ['key', 0, false],
            ['key', null, false],
            ['key', $object, true],
            ['key-type', $object, true],
            ['10', $object, true],
            [spl_object_hash($object), $object, true],
        ];
    }

    /**
     * testValid
     *
     * @covers App\Component\Db\Pool::valid
     * @dataProvider validProvider
     */
    public function testValid($key, $value, $expected)
    {
        $valid = self::getMethod('valid')->invokeArgs(
            $this->instance,
            [$key, $value]
        );
        $this->assertEquals($valid, $expected);
    }
}
