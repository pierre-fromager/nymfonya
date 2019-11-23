<?php

namespace Tests\Component\Auth\Adapters;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Auth\Adapters\Repository as RepositoryAdapter;

/**
 * @covers \App\Component\Auth\Adapters\Repository::<public>
 */
class RepositoryTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../../config/';

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
     * @var RepositoryAdapter
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
      * initialize test
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
        $this->instance = new RepositoryAdapter($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->config = null;
        $this->container = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(RepositoryAdapter::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Auth\Adapters\Repository::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof RepositoryAdapter);
    }

    /**
     * testAuth
     * @covers App\Component\Auth\Adapters\Repository::auth
     */
    public function testAuth()
    {
        $auf = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($auf));
        $this->assertEmpty($auf);
    }
}
