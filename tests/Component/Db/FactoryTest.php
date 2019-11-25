<?php

namespace Tests\Component\Db;

use PHPUnit\Framework\TestCase as PFT;
use \PDO;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Db\Factory;
use App\Component\Db\Pool;

/**
 * @covers App\Component\Db\Factory::<public>
 */
class FactoryTest extends PFT
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
     * @var Factory
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
        $this->setInstance();
    }

    /**
     * instanciate factory
     *
     * @return void
     */
    protected function setInstance()
    {
        $this->instance = new Factory($this->container);
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
        $class = new \ReflectionClass(Factory::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Db\Factory::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Factory);
    }

    /**
     * testGetPool
     * @covers App\Component\Db\Factory::getPool
     */
    public function testGetPool()
    {
        $gpo = self::getMethod('getPool')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gpo instanceof Pool);
    }

    /**
     * testGetConnection
     * @covers App\Component\Db\Factory::getConnection
     * @covers App\Component\Db\Factory::getPool
     */
    public function testGetConnection()
    {
        $id0 = self::getMethod('identity')->invokeArgs(
            $this->instance,
            [self::DB_SLOT_TEST, self::DB_NAME_TEST]
        );
        $this->assertTrue(is_string($id0));
        $this->assertNotEmpty($id0);
        $pool0 = self::getMethod('getPool')->invokeArgs(
            $this->instance,
            []
        );
        $hash0 = spl_object_hash($pool0);
        $this->assertTrue($pool0 instanceof Pool);
        $this->assertFalse(isset($pool0[$id0]));
        $this->assertTrue(
            $this->instance->getConnection(
                self::DB_SLOT_TEST,
                self::DB_NAME_TEST
            ) instanceof PDO
        );
        $pool1 = self::getMethod('getPool')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($pool1 instanceof Pool);
        $this->assertTrue(isset($pool1[$id0]));
        $hash1 =  spl_object_hash($pool1);
        # re-instanciate testing pool service persistence
        $this->setInstance();
        $pool2 = self::getMethod('getPool')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($pool2 instanceof Pool);
        $this->assertTrue(isset($pool2[$id0]));
        $hash2 = spl_object_hash($pool2);
        $this->assertEquals($hash0, $hash1);
        $this->assertEquals($hash1, $hash2);
        $this->assertEquals($pool1, $pool2);
        $this->assertTrue(
            $this->instance->getConnection(
                self::DB_SLOT_TEST,
                self::DB_NAME_TEST
            ) instanceof PDO
        );
        $pool3 = self::getMethod('getPool')->invokeArgs(
            $this->instance,
            []
        );
        $hash3 = spl_object_hash($pool3);
        $this->assertEquals($hash0, $hash3);
        $this->assertEquals($pool2, $pool3);
    }

    /**
     * testConnect
     * @covers App\Component\Db\Factory::connect
     */
    public function testConnect()
    {
        $connArgs = [self::DB_SLOT_TEST, self::DB_NAME_TEST];
        $conn0 = self::getMethod('connect')->invokeArgs(
            $this->instance,
            $connArgs
        );
        $this->assertTrue($conn0 instanceof Factory);
        # Ensure we retrieved existing connexion
        # re-instanciate testing pool service persistence
        $this->setInstance();
        $connArgs = [self::DB_SLOT_TEST, self::DB_NAME_TEST];
        $conn1 = self::getMethod('connect')->invokeArgs(
            $this->instance,
            $connArgs
        );
        $this->assertTrue($conn1 instanceof Factory);
        $this->assertEquals($conn1, $conn0);
    }

    /**
     * testConnectException
     * @covers App\Component\Db\Factory::connect
     */
    public function testConnectException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('connection failed');
        $this->expectExceptionCode(1045);
        $connArgs = [self::DB_SLOT_TEST, 'badnymfonya'];
        self::getMethod('connect')->invokeArgs(
            $this->instance,
            $connArgs
        );
    }

    /**
     * testAdapterParams
     * @covers App\Component\Db\Factory::adapterParams
     */
    public function testAdapterParams()
    {
        $adp = self::getMethod('adapterParams')->invokeArgs(
            $this->instance,
            [self::DB_SLOT_TEST, self::DB_NAME_TEST]
        );
        $this->assertTrue(is_array($adp));
        $this->assertNotEmpty($adp);
    }

    /**
     * testAdapterParamsException
     * @covers App\Component\Db\Factory::adapterParams
     */
    public function testAdapterParamsException()
    {
        $this->expectException(\Exception::class);
        self::getMethod('adapterParams')->invokeArgs(
            $this->instance,
            [self::DB_SLOT_TEST, 'badDbName']
        );
    }

    /**
     * testIdentity
     * @covers App\Component\Db\Factory::identity
     */
    public function testIdentity()
    {
        $ide = self::getMethod('identity')->invokeArgs(
            $this->instance,
            [self::DB_SLOT_TEST, self::DB_NAME_TEST]
        );
        $this->assertTrue(is_string($ide));
        $this->assertNotEmpty($ide);
    }
}
