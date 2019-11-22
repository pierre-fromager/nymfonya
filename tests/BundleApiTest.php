<?php

namespace Tests;

use Nymfonya\Component\HttpFoundation\Tests\Component\Http\KernelTest;
use Nymfonya\Component\Config;
use App\BundleApi;

/**
 * @covers \App\BundleApi::<public>
 */
class BundleApiTest extends KernelTest
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../config/';
    const KERNEL_PATH = '/../src/';
    const KERNEL_NS = '\\App\\Controllers\\';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->instance = new BundleApi(
            Config::ENV_CLI,
            __DIR__ . self::KERNEL_PATH
        );
        $this->instance->setNameSpace(self::KERNEL_NS);
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
     * @covers App\BundleApi::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof BundleApi);
    }
}
