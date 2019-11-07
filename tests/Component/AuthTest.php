<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;
use App\Component\Auth;

/**
 * @covers \App\Component\Auth::<public>
 */
class ToolsAuthTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';

    /**
     * config instance
     *
     * @var Container
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
     * @var Token
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
        $this->instance = new Auth($this->container);
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
     * testInstance
     * @covers App\Component\Auth::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Auth);
    }

    /**
     * testSetAlgo
     * @covers App\Component\Auth::auth
     */
    public function testAuth()
    {
        $aa = $this->instance->auth('badlogin', 'badpassword');
        $this->assertTrue(is_array($aa));
        $this->assertEmpty($aa);
    }
}
