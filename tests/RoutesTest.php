<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Http\Routes;

/**
 * @covers \App\Http\Routes::<public>
 */
class RoutesTest extends PFT
{

    const TEST_ENABLE = true;
    const PATH_ASSETS = 'assets/';

    /**
     * instance
     *
     * @var Routes
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
        $this->instance = new Routes(['r0']);
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
        $class = new \ReflectionClass(Routes::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Http\Routes::__construct
     * @covers App\Http\Routes::get
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Routes);
        $this->assertEquals($this->instance->get(), ['r0']);
    }

    /**
     * testGetSet
     * @covers App\Http\Routes::set
     * @covers App\Http\Routes::get
     */
    public function testGetSet()
    {
        $routesArray = ['r1', 'r2'];
        $this->instance->set($routesArray);
        $this->assertEquals($this->instance->get(), $routesArray);
    }
}
