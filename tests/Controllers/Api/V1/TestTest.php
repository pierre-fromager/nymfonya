<?php

namespace Tests\Controllers\Api\V1;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use App\Controllers\Api\V1\Test as ApiTestControler;

/**
 * @covers \App\Controllers\Api\V1\Test::<public>
 */
class TestTest extends PFT
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
     * instance
     *
     * @var ApiTestControler
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
        $this->instance = new ApiTestControler($this->container);
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
        $class = new \ReflectionClass(ApiTestControler::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Controllers\Api\V1\Test::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof ApiTestControler);
    }

    /**
     * testJwtAction
     * @covers App\Controllers\Api\V1\Test::jwtaction
     */
    public function testJwtAction()
    {
        $this->assertTrue(
            $this->instance->jwtaction() instanceof ApiTestControler
        );
    }

    /**
     * testPokeRelayAction
     * @covers App\Controllers\Api\V1\Test::pokerelay
     */
    public function testPokeRelayAction()
    {
        $this->assertTrue(
            $this->instance->pokerelay() instanceof ApiTestControler
        );
    }

    /**
     * testUploadAction
     * @covers App\Controllers\Api\V1\Test::upload
     */
    public function testUploadAction()
    {
        $this->assertTrue(
            $this->instance->upload() instanceof ApiTestControler
        );
    }

    /**
     * testPokemonApiRelayNoCache
     * @covers App\Controllers\Api\V1\Test::pokemonApiRelay
     * @covers App\Controllers\Api\V1\Test::apiRelayRequest
     */
    public function testPokemonApiRelayNoCache()
    {
        self::getMethod('clearFileCache')->invokeArgs($this->instance, []);
        $par = self::getMethod('pokemonApiRelay')->invokeArgs(
            $this->instance,
            ['https://pokeapi.co/api/v2/pokemon/ditto/']
        );
        $this->assertTrue($par instanceof ApiTestControler);
        $res = self::getMethod('getService')->invokeArgs(
            $this->instance,
            [Response::class]
        );
        $this->assertNotEmpty($res->getContent());
        $this->assertEquals($res->getCode(), Response::HTTP_OK);
    }

    /**
     * testPokemonApiRelayRequest
     * @covers App\Controllers\Api\V1\Test::apiRelayRequest
     */
    public function testPokemonApiRelayRequest()
    {
        $par = self::getMethod('apiRelayRequest')->invokeArgs(
            $this->instance,
            [
                Request::METHOD_POST,
                'https://pokeapi.co/api/v2/pokemon/ditto/',
                [], ['id' => 2]
            ]
        );
        $this->assertTrue($par instanceof ApiTestControler);
    }

    /**
     * testPokemonApiRelayWithCache
     * @covers App\Controllers\Api\V1\Test::pokemonApiRelay
     */
    public function testPokemonApiRelayWithCache()
    {
        $par = self::getMethod('pokemonApiRelay')->invokeArgs(
            $this->instance,
            ['https://pokeapi.co/api/v2/pokemon/ditto/']
        );
        $this->assertTrue($par instanceof ApiTestControler);
        $res = self::getMethod('getService')->invokeArgs(
            $this->instance,
            [Response::class]
        );
        $this->assertNotEmpty($res->getContent());
        $this->assertEquals($res->getCode(), Response::HTTP_OK);
    }

    /**
     * testGetCachePath
     * @covers App\Controllers\Api\V1\Test::getFileCachePath
     */
    public function testGetCachePath()
    {
        $gcp = self::getMethod('getFileCachePath')->invokeArgs($this->instance, []);
        $this->assertTrue(is_string($gcp));
        $this->assertNotEmpty($gcp);
    }

    /**
     * testGetCacheFilename
     * @covers App\Controllers\Api\V1\Test::getCacheFilename
     */
    public function testGetCacheFilename()
    {
        $gcp = self::getMethod('getCacheFilename')->invokeArgs($this->instance, []);
        $this->assertTrue(is_string($gcp));
        $this->assertNotEmpty($gcp);
    }

    /**
     * testExpiredSetGetClearCache
     * @covers App\Controllers\Api\V1\Test::clearFileCache
     * @covers App\Controllers\Api\V1\Test::cacheFileExpired
     * @covers App\Controllers\Api\V1\Test::setFileCache
     * @covers App\Controllers\Api\V1\Test::getFileCache
     */
    public function testExpiredSetGetClearCache()
    {
        $cacheContentString = 'ok content';
        $cc = '';
        self::getMethod('clearFileCache')->invokeArgs($this->instance, []);
        $ce0 = self::getMethod('cacheFileExpired')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($ce0));
        if ($ce0) {
            self::getMethod('setFileCache')->invokeArgs(
                $this->instance,
                [$cacheContentString]
            );
            $cc = $cacheContentString;
        } else {
            $cc =  self::getMethod('getFileCache')->invokeArgs(
                $this->instance,
                []
            );
        }
        $this->assertEquals($cc, $cacheContentString);
        $cc = '';
        $ce1 = self::getMethod('cacheFileExpired')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($ce1));
        if ($ce1) {
            self::getMethod('setFileCache')->invokeArgs(
                $this->instance,
                [$cacheContentString]
            );
        } else {
            $cc =  self::getMethod('getFileCache')->invokeArgs(
                $this->instance,
                []
            );
        }
        $this->assertEquals($cc, $cacheContentString);
        $this->assertTrue($this->instance instanceof ApiTestControler);
    }
}
