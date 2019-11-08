<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use PHPUnit\Framework\MockObject\MockObject;
use App\Config;
use App\Component\Cache\Redis\Adapter;

/**
 * @covers App\Component\Cache\Redis\Adapter::<public>
 */
class ComponentCacheRedisAdapterTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../../config/';

    /**
     * instance
     *
     * @var Adapter
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
        $config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->instance = new Adapter($config);
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
     * @covers App\Component\Cache\Redis\Adapter::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Adapter);
    }

    /**
     * constantsProvider
     * @return Array
     */
    public function constantsProvider()
    {
        return [
            ['_REDIS'],
            ['_HOST'],
            ['_PORT'],
        ];
    }

    /**
     * testConstants
     * @covers App\Component\Cache\Redis\Adapter::__construct
     * @dataProvider constantsProvider
     */
    public function testConstants($k)
    {
        $class = new \ReflectionClass(Adapter::class);
        $this->assertArrayHasKey($k, $class->getConstants());
        unset($class);
    }

    /**
     * testGetClient
     * @covers App\Component\Cache\Redis\Adapter::getClient
     */
    public function testGetClient()
    {
        $this->assertTrue($this->instance->getClient() instanceof \Redis);
    }

    /**
     * testGetClientException
     * @covers App\Component\Cache\Redis\Adapter::getClient
     * @covers App\Component\Cache\Redis\Adapter::isError
     * @covers App\Component\Cache\Redis\Adapter::getErrorCode
     */
    public function testGetClientException()
    {
        $redisConfigParams = [
            Adapter::_PORT => 8888,
            Adapter::_HOST => 'a.0.0.0',
            'timeout' => 0.6
        ];
        $redisConfigValues = function ($arg0) use ($redisConfigParams) {
            if ($arg0 == Adapter::_REDIS) {
                return $redisConfigParams;
            }
        };
        $mockConfig = $this->createMock(\App\Config::class);
        $mockConfig->method('getSettings')->will(
            $this->returnCallback($redisConfigValues)
        );
        $this->assertEquals(
            $mockConfig->getSettings(Adapter::_REDIS),
            $redisConfigParams
        );
        $instance = new Adapter($mockConfig);
        $client = $instance->getClient();
        $this->assertTrue($client instanceof \Redis);
        $this->assertTrue($instance->isError());
        $this->assertEquals(1, $instance->getErrorCode());
    }

    /**
     * testIsError
     * @covers App\Component\Cache\Redis\Adapter::isError
     */
    public function testIsError()
    {
        $this->assertTrue(is_bool($this->instance->isError()));
        $this->assertFalse($this->instance->isError());
    }

    /**
     * testGetErrorCode
     * @covers App\Component\Cache\Redis\Adapter::getErrorCode
     */
    public function testGetErrorCode()
    {
        $this->assertTrue(is_int($this->instance->getErrorCode()));
        $this->assertEquals(0, $this->instance->getErrorCode());
    }

    /**
     * testGetErrorMessage
     * @covers App\Component\Cache\Redis\Adapter::getErrorMessage
     */
    public function testGetErrorMessage()
    {
        $this->assertTrue(is_string($this->instance->getErrorMessage()));
        $this->assertEmpty($this->instance->getErrorMessage());
    }
}
