<?php

namespace Tests\Controllers\Api\V1;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Controllers\Api\V1\Metro as MetroControler;
use App\Model\Repository\Metro\Lines;

/**
 * @covers \App\Controllers\Api\V1\Metro::<public>
 */
class MetroTest extends PFT
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
     * lines model
     *
     * @var Lines
     */
    protected $modelLines;

    /**
     * instance
     *
     * @var MetroControler
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
        $this->modelLines = new Lines($this->container);
        $this->instance = new MetroControler($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->container = null;
        $this->modelLines = null;
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
        $class = new \ReflectionClass(MetroControler::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Controllers\Api\V1\Metro::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof MetroControler);
    }

    /**
     * testLines
     * @covers App\Controllers\Api\V1\Metro::lines
     */
    public function testLines()
    {
        $this->assertTrue(
            $this->instance->lines() instanceof MetroControler
        );
    }

    /**
     * testStations
     * @covers App\Controllers\Api\V1\Metro::stations
     */
    public function testStations()
    {
        $this->assertTrue(
            $this->instance->stations() instanceof MetroControler
        );
    }

    /**
     * testGetQueryResults
     * @covers App\Controllers\Api\V1\Metro::getQueryResults
     */
    public function testGetQueryResults()
    {
        $gqr = self::getMethod('getQueryResults')->invokeArgs(
            $this->instance,
            [
                $this->modelLines->find(['*'], [])
            ]
        );
        $this->assertTrue(is_array($gqr));
        $this->assertNotEmpty($gqr);
    }
}
