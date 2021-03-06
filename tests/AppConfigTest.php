<?php

namespace Tests;

use Nymfonya\Component\Config\Tests\ConfigTest;
use App\Config;

/**
 * @covers \App\Config::<public>
 */
class AppConfigTest extends ConfigTest
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../config/';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
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
    protected function tearDown(): void
    {
        $this->instance = null;
        $this->container = null;
        $this->config = null;
    }

    /**
     * testInstance
     * @covers App\Config::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Config);
    }
}
