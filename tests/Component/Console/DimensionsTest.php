<?php

namespace Tests\Component\Console;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Console\Dimensions;

/**
 * @covers App\Component\Console\Dimensions::<public>
 */
class DimensionsTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Dimensions
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
        $this->instance = new Dimensions();
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
     * testInstance
     * @covers App\Component\Console\Dimensions::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Dimensions);
    }

    /**
     * testSet
     * @covers App\Component\Console\Dimensions::set
     * @covers App\Component\Console\Dimensions::width
     * @covers App\Component\Console\Dimensions::height
     */
    public function testSet()
    {
        $we = 10;
        $he = 20;
        $this->assertTrue(
            $this->instance->set($we, $he) instanceof Dimensions
        );
        $w = $this->instance->width();
        $this->assertNotEmpty($w);
        $this->assertTrue(is_int($w));
        $this->assertEquals($we, $w);
        $h = $this->instance->height();
        $this->assertNotEmpty($h);
        $this->assertTrue(is_int($h));
        $this->assertEquals($he, $h);
    }
}
