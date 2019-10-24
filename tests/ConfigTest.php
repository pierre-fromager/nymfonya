<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;

/**
 * @covers \App\Config::<public>
 */
class ConfigTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../config/';

    /**
     * instance
     *
     * @var Config
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
        $this->instance = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
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
        $class = new \ReflectionClass(Config::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Config::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Config);
    }

    /**
     * testHasEntry
     * @covers App\Config::hasEntry
     */
    public function testHasEntry()
    {
        $this->assertTrue(
            is_bool($this->instance->hasEntry('testentry'))
        );
    }

    /**
     * testGetSettings
     * @covers App\Config::getSettings
     */
    public function testGetSettings()
    {
        $this->assertTrue(
            is_array($this->instance->getSettings())
        );
    }

    /**
     * testGetPath
     * @covers App\Config::getPath
     */
    public function testGetPath()
    {
        $this->assertTrue(
            is_string($this->instance->getPath())
        );
    }

    /**
     * testSetPath
     * @covers App\Config::setPath
     */
    public function testSetPath()
    {
        $this->assertTrue(
            $this->instance->setPath(__DIR__) instanceof Config
        );
    }

    /**
     * testLoadException
     * @covers App\Config::load
     */
    public function testLoadException()
    {
        $this->expectException(\Exception::class);
        $this->instance->setPath('')->load();
    }

    /**
     * testGetFilename
     * @covers App\Config::getFilename
     */
    public function testGetFilename()
    {
        $value = self::getMethod('getFilename')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($value));
        $this->assertNotEmpty($value);
    }

    /**
     * testCheck
     * @covers App\Config::check
     */
    public function testCheck()
    {
        $filename = self::getMethod('getFilename')->invokeArgs(
            $this->instance,
            []
        );
        $check = self::getMethod('check')->invokeArgs(
            $this->instance,
            [$filename]
        );
        $this->assertTrue(is_bool($check));
        $this->assertTrue($check);
    }

    /**
     * testGenEnv
     * @covers App\Config::getEnv
     */
    public function testGenEnv()
    {
        $env = self::getMethod('getEnv')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($env));
    }

    /**
     * testGetAllowedEnv
     * @covers App\Config::getAllowedEnv
     */
    public function testGetAllowedEnv()
    {
        $allowedEnv = self::getMethod('getAllowedEnv')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($allowedEnv));
        $this->assertContains(Config::ENV_DEV, $allowedEnv);
        $this->assertContains(Config::ENV_INT, $allowedEnv);
        $this->assertContains(Config::ENV_PROD, $allowedEnv);
        $this->assertContains(Config::ENV_TEST, $allowedEnv);
        $this->assertContains(Config::ENV_CLI, $allowedEnv);
    }
}
