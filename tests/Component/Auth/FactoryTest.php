<?php

namespace Tests\Component\Auth;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Auth\Factory;
use App\Component\Auth\Adapters\File as FileAdapter;
use App\Component\Auth\Adapters\Config as ConfigAdapter;
use App\Component\Auth\Adapters\Repository as RepositoryAdapter;

/**
 * @covers \App\Component\Auth\Factory::<public>
 */
class FactoryTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';

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
      * @param boolean $badEntry
      * @param boolean $messAdapter
      * @return void
      */
    protected function init(bool $badEntry = false, bool $messAdapter = false)
    {
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        if (true === $badEntry || true === $messAdapter) {
            $stub = $this->getMockBuilder(Config::class)
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->disableArgumentCloning()
                ->disallowMockingUnknownTypes()
                ->getMock();
            if ($badEntry) {
                $stub->method('hasEntry')->willReturn(false);
            }

            if ($messAdapter) {
                $stub->method('hasEntry')->willReturn(true);
                $stub->method('getSettings')->willReturn(
                    ['badCoincoin']
                );
            }

            $this->container->setService(Config::class, $stub);
        }
        $this->instance = new Factory($this->container);
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
        $class = new \ReflectionClass(Factory::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Auth\Factory::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Factory);
    }

    /**
     * testAllowedAdapters
     * @covers App\Component\Auth\Factory::allowedAdapters
     * @covers App\Component\Auth\Factory::auth
     */
    public function testAllowedAdapters()
    {
        $ala = self::getMethod('allowedAdapters')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($ala));
        $this->assertNotEmpty($ala);
    }

    /**
     * testAuthFileAdapter
     * @covers App\Component\Auth\Factory::setAdapter
     * @covers App\Component\Auth\Factory::getAdapter
     * @covers App\Component\Auth\Factory::auth
     */
    public function testAuthFileAdapter()
    {
        $this->instance->setAdapter(FileAdapter::class);
        $auf = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($auf));
        $this->assertEmpty($auf);
        $this->assertTrue(
            $this->instance->getAdapter() instanceof FileAdapter
        );
    }

    /**
     * testAuthRepositoryAdapter
     * @covers App\Component\Auth\Factory::setAdapter
     * @covers App\Component\Auth\Factory::auth
     */
    public function testAuthRepositoryAdapter()
    {
        $this->instance->setAdapter(RepositoryAdapter::class);
        $aur = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($aur));
        $this->assertEmpty($aur);
        $this->assertTrue(
            $this->instance->getAdapter() instanceof RepositoryAdapter
        );
    }

    /**
     * testAuthConfigAdapter
     * @covers App\Component\Auth\Factory::setAdapter
     * @covers App\Component\Auth\Factory::auth
     */
    public function testAuthConfigAdapter()
    {
        $this->instance->setAdapter(ConfigAdapter::class);
        $auc = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($auc));
        $this->assertEmpty($auc);
        $this->assertTrue(
            $this->instance->getAdapter() instanceof ConfigAdapter
        );
    }

    /**
     * testAuthFileAdapterFromConfigOk
     * @covers App\Component\Auth\Factory::setAdapter
     * @covers App\Component\Auth\Factory::auth
     */
    public function testAuthFileAdapterFromConfigOk()
    {
        $this->instance->setAdapter();
        $auf = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($auf));
        $this->assertEmpty($auf);
    }

    /**
     * testAuthBadAdapterException
     * @covers App\Component\Auth\Factory::setAdapter
     * @covers App\Component\Auth\Factory::auth
     */
    public function testAuthBadAdapterException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bad auth adapter classname');
        $this->instance->setAdapter('BaddAdapterClassname');
        $auf = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($auf));
        $this->assertEmpty($auf);
    }

    /**
     * testAuthBadAdapterConfigMissingEntryException
     * @covers App\Component\Auth\Factory::setAdapter
     * @covers App\Component\Auth\Factory::auth
     */
    public function testAuthBadAdapterConfigMissingEntryException()
    {
        $this->init(true);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing auth config');
        $this->instance->setAdapter();
        $auf = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($auf));
        $this->assertEmpty($auf);
    }

    /**
     * testAuthBadAdapterConfigMissingAdapterException
     * @covers App\Component\Auth\Factory::setAdapter
     * @covers App\Component\Auth\Factory::auth
     */
    public function testAuthBadAdapterConfigMissingAdapterException()
    {
        $this->init(false, true);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing auth config adapter');
        $this->instance->setAdapter();
        $auf = $this->instance->auth('login', 'password');
        $this->assertTrue(is_array($auf));
        $this->assertEmpty($auf);
    }
}
