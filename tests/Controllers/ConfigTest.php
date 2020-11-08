<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Controllers\Config as ConfigControler;

/**
 * @covers \App\Controllers\Config::<public>
 */
class ConfigTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';

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
     * @var ConfigControler
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
        $this->instance = new ConfigControler($this->container);
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
        $class = new \ReflectionClass(ConfigControler::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Controllers\Config::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof ConfigControler);
    }

    /**
     * testHelp
     * @covers App\Controllers\Config::help
     */
    public function testHelp()
    {
        $this->assertTrue(
            $this->instance->help() instanceof ConfigControler
        );
    }

    /**
     * testFalse
     * @covers App\Controllers\Config::false
     */
    public function testFalse()
    {
        $this->assertFalse($this->instance->false());
    }

    /**
     * testKeygen
     * @covers App\Controllers\Config::keygen
     */
    public function testKeygen()
    {
        $this->assertTrue(
            $this->instance->keygen() instanceof ConfigControler
        );
    }

    /**
     * testAccount
     * @covers App\Controllers\Config::account
     * @requires PHP 9000
     */
    public function testAccount()
    {
        $this->setOutputCallback(function () {
        });
        $this->assertTrue(
            $this->instance->account() instanceof ConfigControler
        );
    }

   /**
     * testSwaggerdoc
     * @covers App\Controllers\Config::swaggerdoc
     */
    public function testSwaggerdoc()
    {
        $this->setOutputCallback(function () {
        });
        $this->assertTrue(
            $this->instance->swaggerdoc() instanceof ConfigControler
        );
    }

    /**
     * testHasReadLine
     * @covers App\Controllers\Config::hasReadLine
     */
    public function testHasReadLine()
    {
        $hrl = self::getMethod('hasReadLine')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($hrl));
    }

    /**
     * testBaseRootUri
     * @covers App\Controllers\Config::baseRootUri
     */
    public function testBaseRootUri()
    {
        $bri = self::getMethod('baseRootUri')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($bri));
        $this->assertNotEmpty($bri);
    }

    /**
     * testGetActionItem
     * @covers App\Controllers\Config::getActionItem
     */
    public function testGetActionItem()
    {
        $gai = self::getMethod('getActionItem')->invokeArgs(
            $this->instance,
            ['t', 'a', 'd']
        );
        $this->assertTrue(is_array($gai));
        $this->assertNotEmpty($gai);
    }
}
