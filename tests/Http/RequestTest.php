<?php

namespace Tests;

use App\Component\Http\Headers;
use PHPUnit\Framework\TestCase as PFT;
use App\Component\Http\Request;

/**
 * @covers \App\Component\Http\Request::<public>
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
     * @covers App\Component\Http\Request::__construct
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
     * @covers App\Component\Http\Request::__construct
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
     * @covers App\Component\Http\Request::getHost
     */
    public function testGetHost()
    {
        $this->assertTrue(is_string($this->instance->getHost()));
    }

    /**
     * testGetHost
     * @covers App\Component\Http\Request::getMethod
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
     * @covers App\Component\Http\Request::getParams
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
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_GET]
        );
        $this->assertEquals(
            $_GET,
            $this->instance->getParams()
        );
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_POST]
        );
        $this->assertEquals(
            $_POST,
            $this->instance->getParams()
        );
        $this->assertTrue(
            is_array($this->instance->getParams())
        );
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_OPTIONS]
        );
        $this->assertTrue(
            is_array($this->instance->getParams())
        );
        $value = self::getMethod('setContentType')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($value instanceof Request);
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_TRACE]
        );
        $cliParams = self::getMethod('getCliParams')->invokeArgs(
            $this->instance,
            [Request::METHOD_TRACE]
        );
        $this->assertEquals(
            $cliParams,
            $this->instance->getParams()
        );
    }

    /**
     * testSetParams
     * @covers App\Component\Http\Request::setMethod
     * @covers App\Component\Http\Request::setParams
     * @covers App\Component\Http\Request::getParams
     * @covers App\Component\Http\Request::getCliParams
     */
    public function testSetParams()
    {
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_GET]
        );
        $forcedParams = ['p1' => 'v1', 'p2' => 'v2'];
        $req = self::getMethod('setParams')->invokeArgs(
            $this->instance,
            [$forcedParams]
        );
        $this->assertTrue($req instanceof Request);
        $this->assertEquals(
            $forcedParams,
            $this->instance->getParams()
        );
        $req = self::getMethod('setParams')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($req instanceof Request);
        $this->assertEquals(
            [],
            $this->instance->getParams()
        );
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_POST]
        );
        $req = self::getMethod('setParams')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($req instanceof Request);
        $this->assertEquals(
            $_POST,
            $this->instance->getParams()
        );
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_POST]
        );
        $req = self::getMethod('setParams')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($req instanceof Request);
        $this->assertEquals(
            $_POST,
            $this->instance->getParams()
        );
        $this->assertTrue(
            is_array($this->instance->getParams())
        );
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_OPTIONS]
        );
        $req = self::getMethod('setParams')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(
            is_array($this->instance->getParams())
        );
        $value = self::getMethod('setContentType')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($value instanceof Request);
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            [Request::METHOD_TRACE]
        );
        $req = self::getMethod('setParams')->invokeArgs(
            $this->instance,
            []
        );
        $cliParams = self::getMethod('getCliParams')->invokeArgs(
            $this->instance,
            [Request::METHOD_TRACE]
        );
        $this->assertEquals(
            $cliParams,
            $this->instance->getParams()
        );
    }

    /**
     * testSetGetParam
     * @covers App\Component\Http\Request::getParam
     * @covers App\Component\Http\Request::setParam
     * @covers App\Component\Http\Request::getParams
     */
    public function testSetGetParam()
    {
        $req = self::getMethod('setParams')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($req instanceof Request);
        $this->assertTrue(
            is_string($this->instance->getParam('whatever'))
        );
        $this->assertEquals('', $this->instance->getParam('whatever'));
        $req = self::getMethod('setParam')->invokeArgs(
            $this->instance,
            ['whatever', 'whatevervalue']
        );
        $this->assertEquals(
            'whatevervalue',
            $this->instance->getParam('whatever')
        );
        $this->assertEquals(
            ['whatever' => 'whatevervalue'],
            $this->instance->getParams()
        );
    }

    /**
     * testGetRoute
     * @covers App\Component\Http\Request::getRoute
     */
    public function testGetRoute()
    {
        $this->assertTrue(
            is_string($this->instance->getRoute())
        );
    }

    /**
     * testGetFilename
     * @covers App\Component\Http\Request::getFilename
     */
    public function testGetFilename()
    {
        $this->assertTrue(
            is_string($this->instance->getFilename())
        );
    }

    /**
     * testGetUri
     * @covers App\Component\Http\Request::getUri
     * @runInSeparateProcess
     */
    public function testGetUri()
    {
        self::getMethod('setIsCli')->invokeArgs(
            $this->instance,
            [true]
        );
        $this->assertTrue(
            is_string($this->instance->getUri())
        );
        self::getMethod('setIsCli')->invokeArgs(
            $this->instance,
            [false]
        );
        $this->assertTrue(
            is_string($this->instance->getUri())
        );
    }

    /**
     * testGetIp
     * @covers App\Component\Http\Request::getIp
     */
    public function testGetIp()
    {
        $this->assertTrue(
            is_string($this->instance->getIp())
        );
    }

    /**
     * testGetAcceptEncoding
     * @covers App\Component\Http\Request::getAcceptEncoding
     */
    public function testGetAcceptEncoding()
    {
        $this->assertTrue(
            is_string($this->instance->getAcceptEncoding())
        );
    }

    /**
     * testGetContentType
     * @covers App\Component\Http\Request::getContentType
     */
    public function testGetContentType()
    {
        $this->assertTrue(
            is_string($this->instance->getContentType())
        );
    }

    /**
     * testGetServer
     * @covers App\Component\Http\Request::getServer
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
     * testSetIsCli
     * @covers App\Component\Http\Request::setIsCli
     * @covers App\Component\Http\Request::isCli
     * @runInSeparateProcess
     */
    public function testSetIsCli()
    {
        $r = self::getMethod('setIsCli')->invokeArgs(
            $this->instance,
            [true]
        );
        $this->assertTrue($r instanceof Request);
        $this->assertTrue($this->instance->isCli());
        $r = self::getMethod('setIsCli')->invokeArgs(
            $this->instance,
            [false]
        );
        $this->assertTrue($r instanceof Request);
        $this->assertFalse($this->instance->isCli());
    }

    /**
     * testSetMethod
     * @covers App\Component\Http\Request::setMethod
     */
    public function testSetMethod()
    {
        $r = self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            ['POST']
        );
        $this->assertTrue(
            $r instanceof Request
        );
    }

    /**
     * testGetArgs
     * @covers App\Component\Http\Request::getArgs
     * @covers App\Component\Http\Request::getInput
     */
    public function testGetArgs()
    {
        $value = self::getMethod('getArgs')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($value));
        self::getMethod('setMethod')->invokeArgs(
            $this->instance,
            ['GET']
        );
        $this->assertTrue(is_string($value));
    }

    /**
     * testIsJsonContentType
     * @covers App\Component\Http\Request::isJsonContentType
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
     * @covers App\Component\Http\Request::getInput
     * @covers App\Component\Http\Request::setContentType
     */
    public function testGetInput()
    {
        $value = self::getMethod('getInput')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($value));
        self::getMethod('setContentType')->invokeArgs(
            $this->instance,
            ['application/xml']
        );
        $value = self::getMethod('getInput')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($value));
    }

    /**
     * testSetContentType
     * @covers App\Component\Http\Request::setContentType
     */
    public function testSetContentType()
    {
        $value = self::getMethod('setContentType')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($value instanceof Request);
    }

    /**
     * testIsCli
     * @covers App\Component\Http\Request::isCli
     */
    public function testIsCli()
    {
        $value = self::getMethod('isCli')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($value));
    }

    /**
     * testGetCliParams
     * @covers App\Component\Http\Request::getCliParams
     */
    public function testGetCliParams()
    {
        $value = self::getMethod('getCliParams')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($value));
    }

    /**
     * testGetCliParams
     * @covers App\Component\Http\Request::getCliParams
     */
    public function testGetHeaderManager()
    {
        $ret = self::getMethod('getHeaderManager')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ret instanceof Headers);
    }

    /**
     * testSetHeaders
     * @covers App\Component\Http\Request::setHeaders
     */
    public function testSetHeaders()
    {
        $ret = self::getMethod('setHeaders')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ret instanceof Request);
    }
}
