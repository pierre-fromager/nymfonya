<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Http\Request;

/**
 * @covers \App\Http\Request::<public>
 */
class RequestTest extends PFT
{

    const TEST_ENABLE = true;
    const PATH_ASSETS = 'assets/';

    /**
     * instance
     *
     * @var Request
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
        $this->instance = new Request();
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
        $class = new \ReflectionClass(Request::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Http\Request::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Request);
    }

    /**
     * constantsProvider
     * @return Array
     */
    public function constantsProvider()
    {
        return [
            ['_ARGV'],
            ['METHOD_GET'],
            ['METHOD_HEAD'],
            ['METHOD_POST'],
            ['METHOD_PUT'],
            ['METHOD_DELETE'],
            ['METHOD_CONNECT'],
            ['METHOD_OPTIONS'],
            ['METHOD_TRACE'],
            ['METHOD_PATCH'],
            ['REQUEST_METHOD'],
            ['SCRIPT_URL'],
            ['SCRIPT_FILENAME'],
            ['REQUEST_URI'],
            ['HTTP_HOST'],
            ['CONTENT_TYPE'],
            ['REMOTE_ADDR'],
            ['APPLICATION_JSON'],
        ];
    }

    /**
     * testConstants
     * @covers App\Http\Request::__construct
     * @dataProvider constantsProvider
     */
    public function testConstants($k)
    {
        $class = new \ReflectionClass(Request::class);
        $this->assertArrayHasKey($k, $class->getConstants());
        unset($class);
    }

}
