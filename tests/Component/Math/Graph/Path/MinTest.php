<?php

namespace Tests\Component\Math\Graph\Path;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Math\Graph\Path\Min;

/**
 * @covers App\Component\Math\Graph\Path\Min::<public>
 */
class MinTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Min
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
        $this->init([
            'a' => ['b'],
            'b' => ['c'],
            'c' => ['d'],
            'd' => ['z'],
            'z' => ['y'],
            'y' => ['a'],
        ]);
    }

    /**
     * test init
     *
     * @param array $graph
     * @return void
     */
    protected function init(array $graph = [])
    {
        $this->instance = new Min($graph);
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
     * @covers App\Component\Math\Graph\Path\Min::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Min);
    }

    /**
     * testPath
     * @covers App\Component\Math\Graph\Path\Min::path
     */
    public function testPath()
    {
        # 2 edges, 3 nodes
        $pat0 = $this->instance->path('a', 'c');
        $this->assertTrue(is_array($pat0));
        $this->assertNotEmpty($pat0);
        $this->assertEquals(['a', 'b', 'c'], $pat0);
        # 4 edges, 5 nodes
        $pat1 = $this->instance->path('a', 'z');
        $this->assertTrue(is_array($pat1));
        $this->assertNotEmpty($pat1);
        $this->assertEquals(['a', 'b', 'c', 'd', 'z'], $pat1);
        # 1 edges, 2 nodes
        $pat2 = $this->instance->path('y', 'a');
        $this->assertTrue(is_array($pat2));
        $this->assertNotEmpty($pat2);
        $this->assertEquals(['y', 'a'], $pat2);
        # 5 edges, 6 nodes
        $pat3 = $this->instance->path('a', 'y');
        $this->assertTrue(is_array($pat3));
        $this->assertNotEmpty($pat3);
        $this->assertEquals(['a', 'b', 'c', 'd', 'z', 'y'], $pat3);
        # 1 existing node, 1 unknown node
        $pat4 = $this->instance->path('a', 'u');
        $this->assertTrue(is_array($pat4));
        $this->assertEmpty($pat4);
    }
}
