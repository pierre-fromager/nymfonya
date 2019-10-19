<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;
use App\Model\Accounts;

/**
 * @covers \App\Model\Accounts::<public>
 */
class AppModelAccountsTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';
    const ASSET_PATH = '/../../assets/tests/model/';
    const CSV_FILENAME = 'accounts.csv';

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
     * csv filename
     *
     * @var string
     */
    protected $filename;

    /**
     * instance
     *
     * @var Search
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
        /*
        $this->filename = realpath(
            __DIR__ . self::ASSET_PATH . self::CSV_FILENAME
        );*/
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->instance = new Accounts($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
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
        $class = new \ReflectionClass(Accounts::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Model\Accounts::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Accounts);
    }

    /**
     * testReadFromStream
     * @covers App\Model\Accounts::readFromStream
     */
    public function testReadFromStream()
    {
        //$sfn = $this->instance->setFilename($this->filename);
        //$this->assertTrue($sfn instanceof AbstractSearch);
        $sft = $this->instance->setFilter('/^(.*),(.*),(.*),(.*),(.*)/');
        $this->assertTrue($sft instanceof Accounts);
        $sse = $this->instance->setseparator(',');
        $this->assertTrue($sse instanceof Accounts);
        $rff = $this->instance->readFromStream();
        $this->assertTrue($rff instanceof Accounts);
        $datas = $this->instance->get();
        $this->assertTrue(is_array($datas));
        $this->assertNotEmpty($datas);
        $this->assertTrue(count($datas[0]) === 6);
    }

    /**
     * testInit
     * @covers App\Model\Accounts::init
     */
    public function testInit()
    {
        $ini = self::getMethod('init')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ini instanceof Accounts);
    }
}
