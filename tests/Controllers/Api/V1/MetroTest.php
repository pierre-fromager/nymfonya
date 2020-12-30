<?php

namespace Tests\Controllers\Api\V1;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Controllers\Api\V1\Metro as MetroControler;
use App\Model\Repository\Metro\Lines;
use App\Model\Repository\Metro\Stations;
use App\Component\Model\Orm\Orm;

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
     * stations model
     *
     * @var Stations
     */
    protected $modelStations;

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
        $this->modelLines = new Lines($this->container);
        $this->modelStations = new Stations($this->container);
        $this->instance = new MetroControler($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
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
     * @return \ReflectionMethod
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
                $this->modelLines->find([Orm::SQL_ALL], [])
            ]
        );
        $this->assertTrue(is_array($gqr));
        $this->assertNotEmpty($gqr);
    }

    /**
     * testSearch
     * @covers App\Controllers\Api\V1\Metro::search
     */
    public function testSearch()
    {
        $h0 = '2eb621e17cbd97b8';
        $where = ['id' => '*'];
        $query0 = $this->modelLines->find([Orm::SQL_ALL], $where);
        $lineInput = [Lines::_HSRC => $h0];
        $sea0 = self::getMethod('search')->invokeArgs(
            $this->instance,
            [$lineInput, Lines::_HSRC, '', &$query0]
        );
        $this->assertTrue($sea0 instanceof Orm);
        $this->assertEquals(
            $query0->getSql(),
            'SELECT metro_lines.* FROM metro_lines WHERE (metro_lines.hsrc = :v1) ORDER BY metro_lines.id DESC'
        );
        $this->assertEquals(
            $query0->getBuilderValues(),
            [':v1' => $h0]
        );
        $query1 = $this->modelStations->find([Orm::SQL_ALL], $where);
        $stationInput0 =  [
            MetroControler::_LIMIT => 5,
            Stations::_NAME => 'che',
        ];
        $sea1 = self::getMethod('search')->invokeArgs(
            $this->instance,
            [$stationInput0, Stations::_NAME, Orm::OP_LIKE, &$query1]
        );
        $this->assertTrue($sea1 instanceof Orm);
        $stationInput1 =  [];
        $sea2 = self::getMethod('search')->invokeArgs(
            $this->instance,
            [$stationInput1, Stations::_NAME, Orm::OP_LIKE, &$query1]
        );
        $this->assertTrue($sea2 instanceof Orm);
    }

    /**
     * testGetFilteredInput
     * @covers App\Controllers\Api\V1\Metro::getFilteredInput
     */
    public function testGetFilteredInput()
    {
        $where = ['id' => '*'];
        $query0 = $this->modelLines->find([Orm::SQL_ALL], $where);
        $sea0 = self::getMethod('getFilteredInput')->invokeArgs(
            $this->instance,
            [&$query0]
        );
        $this->assertTrue(is_array($sea0));
    }
}
