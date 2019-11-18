<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use \PDO;
use App\Config;
use App\Component\Container;
use App\Model\Repository\Users;
use App\Component\Db\Core;

/**
 * @covers App\Component\Db\Core::<public>
 */
class ComponentDbCoreTest extends PFT
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
        $this->instance = new Core();
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
     * testHydrate
     * @covers App\Component\Db\Core::hydrate
     */
    public function testHydrate()
    {
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
        $run = $this->instance->run($sql, $bindValues);
        $this->assertTrue($run instanceof Core);
    }
}
