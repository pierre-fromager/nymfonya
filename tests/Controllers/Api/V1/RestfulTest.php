<?php

namespace Tests\Controllers\Api\V1;

use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use PHPUnit\Framework\TestCase as PFT;
use App\Controllers\Api\V1\Restful as ApiRestfulControler;

/**
 * @covers \App\Controllers\Api\V1\Restful::<public>
 */
class RestfulTest extends PFT
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
     * @var ApiStatControler
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
        $this->init();
    }

    /**
     * init setup mocking or not request getParams method
     *
     * @param boolean $withMockRequest
     * @return void
     */
    protected function init(bool $withMockRequest = false)
    {
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        if ($withMockRequest) {
            $mockedRequest = $this->createMock(Request::class);
            $mockedRequest->method('getParams')->willReturn(
                ['id' => 3, 'name' => 'coco']
            );
            $this->container->setService(Request::class, $mockedRequest);
        }
        $this->instance = new ApiRestfulControler($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
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
        $class = new \ReflectionClass(ApiRestfulControler::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Controllers\Api\V1\Restful::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof ApiRestfulControler);
    }

    /**
     * testIndexActionNoParams
     * @covers App\Controllers\Api\V1\Restful::index
     * @covers App\Controllers\Api\V1\Restful::getStatusCode
     */
    public function testIndexAction()
    {
        $this->assertTrue(
            $this->instance->index() instanceof ApiRestfulControler
        );
        $err = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertFalse($err);
        $sco = self::getMethod('getStatusCode')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals(Response::HTTP_OK, $sco);
        $this->assertTrue(
            $this->instance->index(['id' => 1]) instanceof ApiRestfulControler
        );
    }

    /**
     * testStoreActionNoParams
     * @covers App\Controllers\Api\V1\Restful::store
     * @covers App\Controllers\Api\V1\Restful::isError
     * @covers App\Controllers\Api\V1\Restful::getStatusCode
     */
    public function testStoreActionNoParams()
    {
        $this->assertTrue(
            $this->instance->store() instanceof ApiRestfulControler
        );
        $err = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue($err);
        $sco = self::getMethod('getStatusCode')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $sco);
    }

    /**
     * testStoreActionNoParams
     * @covers App\Controllers\Api\V1\Restful::store
     * @covers App\Controllers\Api\V1\Restful::isError
     * @covers App\Controllers\Api\V1\Restful::getStatusCode
     */
    public function testStoreActionParams()
    {
        $this->init(true);
        $this->assertTrue(
            $this->instance->store() instanceof ApiRestfulControler
        );
        $err = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertFalse($err);
        $sco = self::getMethod('getStatusCode')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals(Response::HTTP_OK, $sco);
    }

    /**
     * testUpdateActionNoParams
     * @covers App\Controllers\Api\V1\Restful::update
     * @covers App\Controllers\Api\V1\Restful::isError
     * @covers App\Controllers\Api\V1\Restful::getStatusCode
     */
    public function testUpdateActionNoParams()
    {
        $this->assertTrue(
            $this->instance->update() instanceof ApiRestfulControler
        );
        $err = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue($err);
        $sco = self::getMethod('getStatusCode')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $sco);
        $this->assertTrue(
            $this->instance->update(['id' => 1]) instanceof ApiRestfulControler
        );
    }

    /**
     * testUpdateActionParams
     * @covers App\Controllers\Api\V1\Restful::update
     * @covers App\Controllers\Api\V1\Restful::isError
     * @covers App\Controllers\Api\V1\Restful::getStatusCode
     */
    public function testUpdateActionParams()
    {
        $this->init(true);
        $this->assertTrue(
            $this->instance->update() instanceof ApiRestfulControler
        );
        $err = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertFalse($err);
        $sco = self::getMethod('getStatusCode')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals(Response::HTTP_OK, $sco);
    }

    /**
     * testDeleteActionNoParams
     * @covers App\Controllers\Api\V1\Restful::delete
     * @covers App\Controllers\Api\V1\Restful::isError
     * @covers App\Controllers\Api\V1\Restful::getStatusCode
     */
    public function testDeleteActionNoParams()
    {
        $this->assertTrue(
            $this->instance->delete() instanceof ApiRestfulControler
        );
        $err = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue($err);
        $sco = self::getMethod('getStatusCode')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $sco);
        $this->assertTrue(
            $this->instance->delete(['id' => 3]) instanceof ApiRestfulControler
        );
    }

    /**
     * testDeleteActionParams
     * @covers App\Controllers\Api\V1\Restful::delete
     * @covers App\Controllers\Api\V1\Restful::isError
     */
    public function testDeleteActionParams()
    {
        $this->init(true);
        $this->assertTrue(
            $this->instance->delete() instanceof ApiRestfulControler
        );
        $err = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertFalse($err);
    }

    /**
     * testSetResponse
     * @covers App\Controllers\Api\V1\Restful::setResponse
     */
    public function testSetResponse()
    {
        $res = self::getMethod('setResponse')->invokeArgs(
            $this->instance,
            ['toto', 'prout']
        );
        $this->assertTrue($res instanceof ApiRestfulControler);
    }

    /**
     * testIsError
     * @covers App\Controllers\Api\V1\Restful::isError
     */
    public function testIsError()
    {
        $err = self::getMethod('isError')->invokeArgs($this->instance, []);
        $this->assertTrue(is_bool($err));
    }

    /**
     * testGetStatusCode
     * @covers App\Controllers\Api\V1\Restful::getStatusCode
     */
    public function testGetStatusCode()
    {
        $sco = self::getMethod('getStatusCode')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_int($sco));
        $this->assertGreaterThanOrEqual(Response::HTTP_OK, $sco);
    }
}
