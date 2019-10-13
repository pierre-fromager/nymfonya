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

    /**
     * testGetHost
     * @covers App\Http\Request::getHost
     */
    public function testGetHost()
    {
        $this->assertTrue(is_string($this->instance->getHost()));
    }

    /**
     * testGetHost
     * @covers App\Http\Request::getMethod
     */
    public function testGetMethod()
    {
        $this->assertNotEmpty($this->instance->getMethod());
        $this->assertEquals(
            Request::METHOD_TRACE,
            $this->instance->getMethod()
        );
    }

    /**
     * testGetParams
     * @covers App\Http\Request::getParams
     */
    public function testGetParams()
    {
        $this->assertTrue(
            is_array($this->instance->getParams())
        );
        $this->assertEquals(
            [],
            $this->instance->getParams()
        );
    }

    /**
     * testGetParam
     * @covers App\Http\Request::getParam
     */
    public function testGetParam()
    {
        $this->assertTrue(
            is_string($this->instance->getParam('whatever'))
        );
    }

    /**
     * testGetRoute
     * @covers App\Http\Request::getRoute
     */
    public function testGetRoute()
    {
        $this->assertTrue(
            is_string($this->instance->getRoute())
        );
    }

    /**
     * testGetFilename
     * @covers App\Http\Request::getFilename
     */
    public function testGetFilename()
    {
        $this->assertTrue(
            is_string($this->instance->getFilename())
        );
    }

    /**
     * testGetUri
     * @covers App\Http\Request::getUri
     */
    public function testGetUri()
    {
        $this->assertTrue(
            is_string($this->instance->getUri())
        );
    }

    /**
     * testGetIp
     * @covers App\Http\Request::getIp
     */
    public function testGetIp()
    {
        $this->assertTrue(
            is_string($this->instance->getIp())
        );
    }

    /**
     * testGetAcceptEncoding
     * @covers App\Http\Request::getAcceptEncoding
     */
    public function testGetAcceptEncoding()
    {
        $this->assertTrue(
            is_string($this->instance->getAcceptEncoding())
        );
    }

    /**
     * testGetContentType
     * @covers App\Http\Request::getContentType
     */
    public function testGetContentType()
    {
        $this->assertTrue(
            is_string($this->instance->getContentType())
        );
    }

    /**
     * testGetServer
     * @covers App\Http\Request::getServer
     */
    public function testGetServer()
    {
        $args = [Request::REQUEST_METHOD];
        $value = self::getMethod('getServer')->invokeArgs(
            $this->instance,
            $args
        );
        $this->assertTrue(is_string($value));
        $args = ['REQUEST_TIME'];
        $value = self::getMethod('getServer')->invokeArgs(
            $this->instance,
            $args
        );
        $this->assertNotEmpty($value);
    }

    /**
     * testGetArgs
     * @covers App\Http\Request::getArgs
     */
    public function testGetArgs()
    {
        $value = self::getMethod('getArgs')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($value));
    }

    /**
     * testIsJsonContentType
     * @covers App\Http\Request::isJsonContentType
     */
    public function testIsJsonContentType()
    {
        $value = self::getMethod('isJsonContentType')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($value));
    }

    /**
     * testGetInput
     * @covers App\Http\Request::getInput
     */
    public function testGetInput()
    {
        $value = self::getMethod('getInput')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($value));
    }
}
