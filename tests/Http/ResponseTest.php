<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Http\Headers;
use App\Http\Response;

/**
 * @covers \App\Http\Response::<public>
 */
class ResponseTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Response
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
        $this->instance = new Response();
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
        $class = new \ReflectionClass(Response::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Http\Response::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Response);
    }

    /**
     * constantsProvider
     * @return Array
     */
    public function constantsProvider()
    {
        return [
            ['_CLI'],
            ['_ERROR'],
            ['_ERROR_CODE'],
            ['_ERROR_MSG'],
        ];
    }

    /**
     * testConstants
     * @covers App\Http\Response::__construct
     * @dataProvider constantsProvider
     */
    public function testConstants($k)
    {
        $class = new \ReflectionClass(Response::class);
        $this->assertArrayHasKey($k, $class->getConstants());
        unset($class);
    }

    /**
     * testGetHeaderManager
     * @covers App\Http\Response::getHeaderManager
     */
    public function testGetHeaderManager()
    {
        $this->assertTrue(
            $this->instance->getHeaderManager() instanceof Headers
        );
    }

    /**
     * testSetGetCode
     * @covers App\Http\Response::setCode
     * @covers App\Http\Response::getCode
     */
    public function testSetGetCode()
    {
        $this->assertEquals(
            $this->instance->getCode(),
            Response::HTTP_NOT_FOUND
        );
        $this->assertTrue(
            $this->instance->setCode(200) instanceof Response
        );
        $this->assertEquals(
            $this->instance->getCode(),
            Response::HTTP_OK
        );
    }

    /**
     * testSetContent
     * @covers App\Http\Response::setContent
     */
    public function testSetContent()
    {
        $this->assertTrue(
            $this->instance->setContent([]) instanceof Response
        );
        $this->assertTrue(
            $this->instance->setContent(
                json_encode([])
            ) instanceof Response
        );
    }

    /**
     * testSend
     * @covers App\Http\Response::send
     * @covers App\Http\Response::setIsCli
     * @runInSeparateProcess
     */
    public function testSend()
    {
        $this->setOutputCallback(function () {
        });
        $this->assertTrue(
            $this->instance->send() instanceof Response
        );
        self::getMethod('setIsCli')->invokeArgs(
            $this->instance,
            [false]
        );
        $this->assertTrue(
            $this->instance->send() instanceof Response
        );
    }
}
