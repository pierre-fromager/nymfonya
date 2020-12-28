<?php

namespace Tests\Reuse\Controllers;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Request;
use App\Reuse\Controllers\Cacheable;

/**
 * @covers \App\Reuse\Controllers\Cacheable::<public>
 */
class CacheableTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';

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
     * @var Kernel
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
        $this->instance = new class ($this->container) extends Cacheable
        {
            public function __construct(Container $container)
            {
                parent::__construct($container);
            }
        };
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
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Cacheable::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Reuse\Controllers\Cacheable::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Cacheable);
    }

    /**
     * testCacheFileExpired
     * @covers App\Reuse\Controllers\Cacheable::clearFileCache
     * @covers App\Reuse\Controllers\Cacheable::setFileCache
     * @covers App\Reuse\Controllers\Cacheable::cacheFileExpired
     */
    public function testCacheFileExpired()
    {
        # test expired true we clear all caches first
        self::getMethod('clearFileCache')->invokeArgs(
            $this->instance,
            [false]
        );
        $gpa = self::getMethod('cacheFileExpired')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($gpa));
        $this->assertTrue($gpa);
        # test expired false we set a cache
        self::getMethod('clearFileCache')->invokeArgs(
            $this->instance,
            [true]
        );
        self::getMethod('setFileCache')->invokeArgs(
            $this->instance,
            ['some content']
        );
        $gpa = self::getMethod('cacheFileExpired')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($gpa));
        $this->assertFalse($gpa);
    }

    /**
     * testGetFileCachePath
     * @covers App\Reuse\Controllers\Cacheable::setFileCache
     * @covers App\Reuse\Controllers\Cacheable::getFileCache
     */
    public function testGetFileCache()
    {
        $sfc = self::getMethod('setFileCache')->invokeArgs(
            $this->instance,
            ['some content']
        );
        $this->assertTrue(is_int($sfc));
        $gfc = self::getMethod('getFileCache')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($gfc));
    }

    /**
     * testGetCacheFilename
     * @covers App\Reuse\Controllers\Cacheable::getCacheFilename
     */
    public function testGetCacheFilename()
    {
        $gfc = self::getMethod('getCacheFilename')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($gfc));
        $this->assertNotEmpty($gfc);
    }

    /**
     * testGetFileCachePath
     * @covers App\Reuse\Controllers\Cacheable::getFileCachePath
     */
    public function testGetFileCachePath()
    {
        $gpa = self::getMethod('getFileCachePath')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($gpa));
        $this->assertNotEmpty($gpa);
    }

    /**
     * testInitRedisClient
     * @covers App\Reuse\Controllers\Cacheable::initRedisClient
     * @covers App\Reuse\Controllers\Cacheable::cacheRedisExpired
     */
    public function testInitRedisClient()
    {
        self::getMethod('initRedisClient')->invokeArgs(
            $this->instance,
            [$this->container]
        );
        $ise = self::getMethod('cacheRedisExpired')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($ise));
    }

    /**
     * testSetRedisCacheTtl
     * @covers App\Reuse\Controllers\Cacheable::initRedisClient
     * @covers App\Reuse\Controllers\Cacheable::setRedisCacheTtl
     * @covers App\Reuse\Controllers\Cacheable::setRedisCache
     * @covers App\Reuse\Controllers\Cacheable::getRedisCache
     */
    public function testSetRedisCacheTtl()
    {
        self::getMethod('initRedisClient')->invokeArgs(
            $this->instance,
            [$this->container]
        );
        self::getMethod('setRedisCacheTtl')->invokeArgs(
            $this->instance,
            [10]
        );
        $expected = 'testContent';
        self::getMethod('setRedisCache')->invokeArgs(
            $this->instance,
            [$expected]
        );

        $content = self::getMethod('getRedisCache')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals($content, $expected);
    }

    /**
     * testGetRedisCacheFilename
     * @covers App\Reuse\Controllers\Cacheable::getRedisCacheFilename
     */
    public function testGetRedisCacheFilename()
    {
        $grcp = self::getMethod('getRedisCacheFilename')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($grcp));
        $this->assertNotEmpty($grcp);
    }

    /**
     * testGetRedisCachePath
     * @covers App\Reuse\Controllers\Cacheable::getRedisCachePath
     */
    public function testGetRedisCachePath()
    {
        $grcp = self::getMethod('getRedisCachePath')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($grcp));
        $this->assertNotEmpty($grcp);
    }

    /**
     * testClearRedisCache
     * @covers App\Reuse\Controllers\Cacheable::setRedisCacheTtl
     * @covers App\Reuse\Controllers\Cacheable::setRedisCache
     * @covers App\Reuse\Controllers\Cacheable::clearRedisCache
     * @covers App\Reuse\Controllers\Cacheable::getRedisCache
     */
    public function testClearRedisCache()
    {

        $payload = [
            'payload' => [
                'data' => 'testContent'
            ]
        ];
        self::getMethod('setRedisCacheTtl')->invokeArgs(
            $this->instance,
            [100]
        );

        # Key auto based on request url, no key provided for clear
        self::getMethod('setRedisCache')->invokeArgs(
            $this->instance,
            [$payload]
        );
        $grcp = self::getMethod('getRedisCache')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals($grcp, $payload);
        self::getMethod('clearRedisCache')->invokeArgs(
            $this->instance,
            []
        );
        $grcp = self::getMethod('getRedisCache')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_null($grcp));

        # Key auto based on request url, key * for clear all
        self::getMethod('setRedisCache')->invokeArgs(
            $this->instance,
            [$payload]
        );
        $grcp = self::getMethod('getRedisCache')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals($grcp, $payload);
        self::getMethod('clearRedisCache')->invokeArgs(
            $this->instance,
            ['*']
        );
        $grcp = self::getMethod('getRedisCache')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_null($grcp));

        # Key given for clear
        $key = 'toto';
        self::getMethod('setRedisCache')->invokeArgs(
            $this->instance,
            [$payload, $key]
        );
        $grcp = self::getMethod('getRedisCache')->invokeArgs(
            $this->instance,
            [$key]
        );
        $this->assertEquals($grcp, $payload);
        self::getMethod('clearRedisCache')->invokeArgs(
            $this->instance,
            [$key]
        );
        $grcp = self::getMethod('getRedisCache')->invokeArgs(
            $this->instance,
            [$key]
        );
        $this->assertTrue(is_null($grcp));
    }
}
