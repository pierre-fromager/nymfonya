<?php

namespace Tests\Component\Console;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Console\Input;

/**
 * @covers App\Component\Console\Input::<public>
 */
class InputTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Input
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
        $this->instance = new Input(Input::STREAM_MEMORY);
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
        $class = new \ReflectionClass(Input::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Console\Input::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Input);
    }

    /**
     * testValue
     * @covers App\Component\Console\Input::value
     */
    public function testValue()
    {
        $value = "0\n";
        $readValue = $this->instance->value($value);
        $this->assertNotEmpty($readValue);
        $this->assertEquals($readValue, $value);
    }

    /**
     * testGetMaxLength
     * @covers App\Component\Console\Input::getMaxLength
     */
    public function testGetMaxLength()
    {
        $gml = self::getMethod('getMaxLength')->invokeArgs($this->instance, []);
        $this->assertNotEmpty($gml);
        $this->assertEquals($gml, 1);
    }

    /**
     * testGetStreamHandler
     * @covers App\Component\Console\Input::getStreamHandler
     */
    public function testGetStreamHandler()
    {
        $gsh = self::getMethod('getStreamHandler')->invokeArgs($this->instance, []);
        $this->assertTrue(is_resource($gsh));
    }

    /**
     * testGetStreamName
     * @covers App\Component\Console\Input::getStreamName
     */
    public function testGetStreamName()
    {
        $gsn = self::getMethod('getStreamName')->invokeArgs($this->instance, []);
        $this->assertTrue(is_string($gsn));
        $this->assertNotEmpty($gsn);
    }   
    
    /**
     * testOpenCloseStream
     * @covers App\Component\Console\Input::openStream
     * @covers App\Component\Console\Input::closeStream
     */
    public function testOpenCloseStream()
    {
        $osm = self::getMethod('openStream')->invokeArgs($this->instance, []);
        $this->assertTrue($osm instanceof Input);
        $csm = self::getMethod('closeStream')->invokeArgs($this->instance, []);
        $this->assertTrue($csm instanceof Input);
    }      
    
}
