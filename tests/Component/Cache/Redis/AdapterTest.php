<?php

namespace Tests\Component\Cache\Redis;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use App\Component\Cache\Redis\Adapter;

/**
 * @covers App\Component\Cache\Redis\Adapter::<public>
 */
class AdapterTest extends PFT
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
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Adapter::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
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
        $this->assertTrue(
            $this->instance->getClient() instanceof \Redis
        );
        $this->assertFalse($this->instance->isError());
    }

    /**
     * testGetClientException
     * @covers App\Component\Cache\Redis\Adapter::getClient
     * @covers App\Component\Cache\Redis\Adapter::isError
     * @covers App\Component\Cache\Redis\Adapter::getErrorCode
     */
    public function testGetClientException()
    {
        $badPort = 6379;
        $badHost = 'a.0.0.0';
        self::getMethod('applyConfig')->invokeArgs(
            $this->instance,
            [$badHost, $badPort]
        );
        $client = $this->instance->getClient();
        $this->assertTrue($client instanceof \Redis);
        $this->assertTrue($this->instance->isError());
        $this->assertEquals(1, $this->instance->getErrorCode());
        $expectMessage = (version_compare(phpversion(), '7.1', '<'))
            ? 'Connection refused'
            : 'php_network_getaddresses: getaddrinfo failed: Name or service not known';
        $this->assertEquals(
            $expectMessage,
            $this->instance->getErrorMessage()
        );
    }

    /**
     * testGetClientNoConnect
     * @covers App\Component\Cache\Redis\Adapter::getClient
     * @covers App\Component\Cache\Redis\Adapter::isError
     * @covers App\Component\Cache\Redis\Adapter::getErrorCode
     */
    public function testGetClientNoConnect()
    {
        $badPort = 6378;
        self::getMethod('applyConfig')->invokeArgs(
            $this->instance,
            ['localhost', $badPort]
        );
        $client = $this->instance->getClient();
        $this->assertTrue($client instanceof \Redis);
        $this->assertTrue($this->instance->isError());
        $this->assertEquals(1, $this->instance->getErrorCode());
        $this->assertEquals(
            'Connection refused',
            $this->instance->getErrorMessage()
        );
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

    /**
     * testApplyConfig
     * @covers App\Component\Cache\Redis\Adapter::applyConfig
     */
    public function testApplyConfig()
    {
        $aco = self::getMethod('applyConfig')->invokeArgs(
            $this->instance,
            ['localhost', 6379]
        );
        $this->assertTrue($aco instanceof Adapter);
    }
}
