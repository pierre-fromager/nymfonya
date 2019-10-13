<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Http\Response;

/**
 * @covers \App\Http\Response::<public>
 */
class ResponseTest extends PFT
{

    const TEST_ENABLE = true;
    const PATH_ASSETS = 'assets/';

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
 
}
