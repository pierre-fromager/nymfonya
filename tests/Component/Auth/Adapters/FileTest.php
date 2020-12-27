<?php

namespace Tests\Component\Auth\Adapters;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Auth\Adapters\File as FileAdapter;
use Tests\Fake\Credential;

/**
 * FileTest
 *
 * test auth file adapter.
 * Results may differ from config accounts values.
 *
 * @covers \App\Component\Auth\Adapters\File::<public>
 */
class FileTest extends PFT
{
    use Credential;

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
     * @var FileAdapter
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
        $this->instance = new FileAdapter($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
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
        $class = new \ReflectionClass(FileAdapter::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Auth\Adapters\File::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof FileAdapter);
    }

    /**
     * testAuthOk
     * @covers App\Component\Auth\Adapters\File::auth
     */
    public function testAuthOk()
    {
        $auf = $this->instance->auth(
            $this->loginOk(),
            $this->passwordOk()
        );
        $this->assertTrue(is_array($auf));
        $this->assertNotEmpty($auf);
    }

    /**
     * testAuthNok
     * @covers App\Component\Auth\Adapters\File::auth
     */
    public function testAuthNok()
    {
        $auf = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($auf));
        $this->assertEmpty($auf);
    }

    /**
     * testGetById
     * @covers App\Component\Auth\Adapters\File::getById
     */
    public function testGetById()
    {
        $gbi0 = $this->instance->getById(0);
        $this->assertTrue(is_array($gbi0));
        $this->assertEmpty($gbi0);
        $gbi1 = $this->instance->getById(1);
        $this->assertTrue(is_array($gbi1));
        $this->assertNotEmpty($gbi1);
    }
}
