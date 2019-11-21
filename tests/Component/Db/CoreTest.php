<?php

namespace Tests\Component\Db;

use PHPUnit\Framework\TestCase as PFT;
use \PDO;
use Nymfonya\Component\Config;
use App\Component\Container;
use App\Component\Http\Kernel;
use App\Model\Repository\Users;
use App\Component\Db\Core;

/**
 * @covers App\Component\Db\Core::<public>
 */
class CoreTest extends PFT
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
     * user repository
     *
     * @var Users
     */
    protected $userRepository;

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
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->userRepository = new Users($this->container);
        $this->instance = new Core($this->container);
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
     * return a valid connection
     *
     * @return PDO
     */
    protected function getConnection(): PDO
    {
        $dbConfig = $this->config->getSettings(Config::_DB);
        $adapterParams = $dbConfig[self::DB_SLOT_TEST][self::DB_NAME_TEST];
        $adapter = new \App\Component\Db\Adapter\PdoMysql(
            self::DB_NAME_TEST,
            $adapterParams
        );
        return $adapter->connect()->getConnection();
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Core::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Db\Core::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Core);
    }

    /**
     * testFromOrm
     * @covers App\Component\Db\Core::fromOrm
     */
    public function testFromOrm()
    {
        $this->assertTrue(
            $this->instance->fromOrm($this->userRepository)
                instanceof Core
        );
    }

    /**
     * testGetRowset
     * @covers App\Component\Db\Core::getRowset
     */
    public function testGetRowset()
    {
        $rowset = $this->instance->getRowset();
        $this->assertTrue(is_array($rowset));
        $this->assertEmpty($this->instance->getRowset());
    }

    /**
     * testHydrateNoStatement
     * @covers App\Component\Db\Core::hydrate
     */
    public function testHydrateNoStatement()
    {
        $this->assertTrue(
            $this->instance->hydrate() instanceof Core
        );
    }

    /**
     * testHydrateWithStatement
     * @covers App\Component\Db\Core::hydrate
     */
    public function testHydrateWithStatement()
    {
        self::getMethod('setConnection')->invokeArgs(
            $this->instance,
            [$this->getConnection()]
        );
        $this->instance->run('select * from users');
        $this->assertTrue(
            $this->instance->hydrate() instanceof Core
        );
    }

    /**
     * testRun
     * @covers App\Component\Db\Core::fromOrm
     * @covers App\Component\Db\Core::run
     */
    public function testRun()
    {
        $this->instance->fromOrm($this->userRepository);
        $this->userRepository->find(['id', 'name'], ['id' => 0]);
        $sql = $this->userRepository->getSql();
        $bindValues = $this->userRepository->getBuilderValues();
        $bindTypes = [':v1' => \PDO::PARAM_BOOL];
        $run = $this->instance->run($sql, $bindValues, $bindTypes);
        $this->assertTrue($run instanceof Core);
    }

    /**
     * testRunErrorExecute
     * @covers App\Component\Db\Core::run
     * @covers App\Component\Db\Core::isError
     * @covers App\Component\Db\Core::bindArray
     */
    public function testRunErrorExecute()
    {
        $ise0 = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($ise0));
        $this->assertFalse($ise0);
        self::getMethod('setConnection')->invokeArgs(
            $this->instance,
            [$this->getConnection()]
        );
        $sql = 'select * from badtable';
        $run = $this->instance->run($sql, []);
        $this->assertTrue($run instanceof Core);
        $ise1 = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue($ise1);
        self::getMethod('resetError')->invokeArgs($this->instance, []);
        // bindArray exception
        $sql = 'select * from users where id = :id';
        $bindValues = [':idx' => 0];
        $run = $this->instance->run($sql, $bindValues);
        $ise2 = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue($ise2);
    }

    /**
     * testSetConnection
     * @covers App\Component\Db\Core::setConnection
     */
    public function testSetConnection()
    {
        $sco = self::getMethod('setConnection')->invokeArgs(
            $this->instance,
            [$this->getConnection()]
        );
        $this->assertTrue($sco instanceof Core);
    }

    /**
     * testIsError
     * @covers App\Component\Db\Core::isError
     */
    public function testIsError()
    {
        $ise = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($ise));
        $this->assertFalse($ise);
    }

    /**
     * testGetErrorCodeMessage
     * @covers App\Component\Db\Core::isError
     * @covers App\Component\Db\Core::getErrorCode
     * @covers App\Component\Db\Core::getErrorMessage
     */
    public function testGetErrorCodeMessage()
    {
        $ise = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($ise));
        $this->assertFalse($ise);
        $ger = self::getMethod('getErrorCode')->invokeArgs($this->instance, []);
        $gem = self::getMethod('getErrorMessage')->invokeArgs($this->instance, []);
        $this->assertTrue(is_int($ger));
        $this->assertTrue(is_string($gem));
        $this->assertEmpty($gem);
    }

    /**
     * testResetError
     * @covers App\Component\Db\Core::resetError
     * @covers App\Component\Db\Core::isError
     * @covers App\Component\Db\Core::setError
     */
    public function testResetError()
    {
        $ise0 = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($ise0));
        $this->assertFalse($ise0);
        $serr = self::getMethod('setError')->invokeArgs(
            $this->instance,
            [true, 100, 'hundred error']
        );
        $this->assertTrue($serr instanceof Core);
        $ise1 = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($ise1));
        $this->assertTrue($ise1);
        $res0 = self::getMethod('resetError')->invokeArgs($this->instance, []);
        $this->assertTrue($res0 instanceof Core);
        $ise2 = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($ise2));
        $this->assertFalse($ise2);
    }
}
