<?php

namespace Tests\Component\Console;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Console\Terminal;

/**
 * @covers App\Component\Console\Terminal::<public>
 */
class TerminalTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Terminal
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
        $this->instance = new Terminal();
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
        $class = new \ReflectionClass(Terminal::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Console\Terminal::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Terminal);
    }

    /**
     * testSetDimensions
     * @covers App\Component\Console\Terminal::setDimensions
     */
    public function testSetDimensions()
    {
        $sdi = self::getMethod('setDimensions')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($sdi instanceof Terminal);
    }

    /**
     * testIsWindows
     * @covers App\Component\Console\Terminal::isWindows
     */
    public function testIsWindows()
    {
        $iwi = self::getMethod('isWindows')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_bool($iwi));
    }
}
