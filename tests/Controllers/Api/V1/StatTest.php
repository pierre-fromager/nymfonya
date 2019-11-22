<?php

namespace Tests\Controllers\Api\V1;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Controllers\Api\V1\Stat as ApiStatControler;

/**
 * @covers \App\Controllers\Api\V1\Stat::<public>
 */
class StatTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../../config/';

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
     * instance
     *
     * @var ApiStatControler
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
        $this->instance = new ApiStatControler($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->container = null;
        $this->config = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Auth::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Controllers\Api\V1\Stat::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof ApiStatControler);
    }

    /**
     * testOpcacheAction
     * @covers App\Controllers\Api\V1\Stat::opcache
     */
    public function testOpcacheAction()
    {
        $this->assertTrue(
            $this->instance->opcache() instanceof ApiStatControler
        );
    }

    /**
     * testFilecacheAction
     * @covers App\Controllers\Api\V1\Stat::filecache
     */
    public function testFilecacheAction()
    {
        $this->assertTrue(
            $this->instance->filecache() instanceof ApiStatControler
        );
    }
}
