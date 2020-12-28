<?php

namespace Tests\Model\Repository\Metro;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Model\Orm\Orm;
use App\Component\Model\Orm\IOrm;
use App\Model\Repository\Metro\Stations;

/**
 * @covers \App\Model\Repository\Metro\Stations::<public>
 */
class StationsTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../../config/';
    const VALID_LOGIN = 'admin@domain.tld';
    const VALID_PASSWORD = 'adminadmin';

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
     * @var Stations
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
        $this->instance = new Stations($this->container);
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
     * testInstance
     * @covers App\Model\Repository\Metro\Stations::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Stations);
        $this->assertEquals(
            get_parent_class($this->instance),
            Orm::class
        );
        $this->assertTrue(in_array(
            IOrm::class,
            class_implements(Stations::class)
        ));
    }

    /**
     * testGetPrimary
     * @covers App\Model\Repository\Metro\Stations::getPrimary
     */
    public function testGetPrimary()
    {
        $this->assertTrue($this->instance instanceof Stations);
        $this->assertEquals(
            $this->instance->getPrimary(),
            'id'
        );
    }
}
