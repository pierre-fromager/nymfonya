<?php

namespace Tests\Component\Migration;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Migration\Users;
use App\Component\Db\Migration;

/**
 * @covers \App\Component\Migration\Users::<public>
 */
class UsersTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';
    const PAYLOAD = [0, 'gogo', 'dancer'];

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
     * @var Users
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
        $this->instance = new Users($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->config = null;
        $this->request = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Users::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Migration\Users::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Users);
    }

    /**
     * testCanMigrate
     * @covers App\Component\Migration\Users::canMigrate
     */
    public function testCanMigrate()
    {
        $cam = self::getMethod('canMigrate')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($cam));
    }

    /**
     * testGetSqlCreate
     * @covers App\Component\Migration\Users::runCreate
     */
    public function testGetSqlCreate()
    {
        $gsc = self::getMethod('runCreate')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gsc instanceof Migration);
    }

    /**
     * testGetSqlInsert
     * @covers App\Component\Migration\Users::runInsert
     */
    public function testGetSqlInsert()
    {
        $gsi = self::getMethod('runInsert')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gsi instanceof Migration);
    }
}
