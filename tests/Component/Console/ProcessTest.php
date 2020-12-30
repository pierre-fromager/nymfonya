<?php

namespace Tests\Component\Console;

use App\Component\Console\Dimensions;
use PHPUnit\Framework\TestCase as PFT;
use App\Component\Console\Process;

/**
 * @covers App\Component\Console\Process::<public>
 */
class ProcessTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Process
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
        $this->instance = new Process();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
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
        $class = new \ReflectionClass(Process::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Console\Process::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Process);
    }

    /**
     * testSetCommand
     * @covers App\Component\Console\Process::setCommand
     */
    public function testSetCommand()
    {
        $this->assertTrue($this->instance->setCommand('') instanceof Process);
    }

    /**
     * testRun
     *
     * @covers App\Component\Console\Process::setCommand
     * @covers App\Component\Console\Process::run
     */
    public function testRun()
    {
        $cor0 = $this->instance->setCommand('')->run();
        $this->assertTrue($cor0 instanceof Process);
        $this->assertEmpty((string) $this->instance);
        $this->assertFalse($this->instance->isError());
        $cor1 = $this->instance->setCommand('ls')->run();
        $this->assertTrue($cor1 instanceof Process);
        $this->assertNotEmpty((string) $this->instance);
        $this->assertFalse($this->instance->isError());
        $cor2 = $this->instance->setCommand('cat /etc/password')->run();
        $this->assertTrue($cor2 instanceof Process);
        $this->assertEmpty((string) $this->instance);
        $this->assertTrue($this->instance->isError());
    }

    /**
     * testIsError
     * @covers App\Component\Console\Process::isError
     */
    public function testIsError()
    {
        $this->assertTrue(is_bool($this->instance->isError()));
    }

    /**
     * testGetErrorMessage
     * @covers App\Component\Console\Process::getErrorMessage
     */
    public function testGetErrorMessage()
    {
        $this->assertTrue(is_string($this->instance->getErrorMessage()));
    }

    /**
     * testToString
     * @covers App\Component\Console\Process::__toString
     */
    public function testToString()
    {
        $this->assertEquals('', (string) $this->instance);
    }

    /**
     * testReset
     * @covers App\Component\Console\Process::reset
     */
    public function testReset()
    {
        $res = self::getMethod('reset')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($res instanceof Process);
    }

    /**
     * testGetDescriptors
     * @covers App\Component\Console\Process::getDescriptors
     */
    public function testGetDescriptors()
    {
        $gde = self::getMethod('getDescriptors')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($gde));
        $this->assertNotEmpty($gde);
    }
}
