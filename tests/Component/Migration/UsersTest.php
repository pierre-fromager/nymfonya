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
    protected function setUp(): void
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
    protected function tearDown(): void
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
     * @covers App\Component\Db\Migration::__construct
     */
    public function testInstance()
    {
        $ise0 = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise0);
        $this->assertTrue($this->instance instanceof Users);
        $ise1 = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise1);
    }

    /**
     * testDropTable
     * @covers App\Component\Migration\Users::dropTable
     */
    public function testDropTable()
    {
        $tae = self::getMethod('tableExists')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($tae));
        $dropped0 = self::getMethod('dropTable')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($dropped0));
        $this->assertEquals($tae, $dropped0);
        $tae1 = self::getMethod('tableExists')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($tae1);
        $dropped1 = self::getMethod('dropTable')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($dropped1);
    }

    /**
     * testCanMigrate
     * @covers App\Component\Migration\Users::canMigrate
     */
    public function testCanMigrate()
    {
        $ise = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise);
        $cam = self::getMethod('canMigrate')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($cam));
        $ise1 = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise1);
    }

    /**
     * testRunCreate
     * @covers App\Component\Migration\Users::runCreate
     * @covers App\Component\Migration\Users::isError
     */
    public function testRunCreate()
    {
        $ise = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise);
        $drop = self::getMethod('dropTable')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($drop));
        $tae0 = self::getMethod('tableExists')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($tae0);
        $gsc = self::getMethod('runCreate')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gsc instanceof Migration);
        $ise1 = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise1);
    }


    /**
     * testRunIndex
     * @covers App\Component\Migration\Users::runIndex
     * @covers App\Component\Migration\Users::isError
     */
    public function testRunIndex()
    {
        $ise = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise);
        $rui = self::getMethod('runIndex')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($rui instanceof Migration);

        $ise1 = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise1);
    }

    /**
     * testRunInsert
     * @covers App\Component\Migration\Users::runInsert
     */
    public function testRunInsert()
    {
        $gsi = self::getMethod('runInsert')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($gsi instanceof Migration);
    }

    /**
     * testGetInsertDatas
     * @covers App\Component\Migration\Users::getInsertDatas
     */
    public function testGetInsertDatas()
    {
        $gid = self::getMethod('getInsertDatas')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($gid));
        $this->assertNotEmpty($gid);
    }

    /**
     * testTableExists
     * @covers App\Component\Migration\Users::tableExists
     */
    public function testTableExists()
    {
        $tae = self::getMethod('tableExists')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($tae));
    }

    /**
     * testMigrate
     * @covers App\Component\Migration\Users::dropTable
     * @covers App\Component\Migration\Users::migrate
     */
    public function testMigrate()
    {
        $ise0 = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise0);
        $drop = self::getMethod('dropTable')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($drop));
        $tae0 = self::getMethod('tableExists')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($tae0);
        $mig = $this->instance->migrate();
        $this->assertTrue($mig instanceof Migration);
        $tae1 = self::getMethod('tableExists')->invokeArgs(
            $this->instance,
            []
        );
        $ise1 = self::getMethod('isError')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertFalse($ise1);
        $this->assertTrue($tae1);
    }
}
