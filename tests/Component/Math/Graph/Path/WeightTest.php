<?php

namespace Tests\Component\Math\Graph\Path;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Math\Graph\Path\Weight;

/**
 * @covers App\Component\Math\Graph\Path\Weight::<public>
 */
class WeightTest extends PFT
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
        $graph0 = [
            'a' => ['b' => 1],
            'b' => ['c' => 2],
            'c' => ['a' => 4],
        ];
        $this->init($graph0);
    }

    /**
     * test init
     *
     * @param array $graph
     * @return void
     */
    protected function init(array $graph = [])
    {
        $this->instance = new Weight($graph);
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
        $class = new \ReflectionClass(Weight::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Math\Graph\Path\Weight::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Weight);
    }

    /**
     * testPath
     * @covers App\Component\Math\Graph\Path\Weight::path
     * @covers App\Component\Math\Graph\Path\Weight::search
     * @covers App\Component\Math\Graph\Path\Weight::processPath
     * @covers App\Component\Math\Graph\Path\Weight::distance
     */
    public function testPath()
    {
        $path0 = $this->instance->path('a', 'c');
        $this->assertTrue(is_array($path0));
        $this->assertNotEmpty($path0);
        $this->assertEquals(['a', 'b', 'c'], $path0);
        $dis0 = $this->instance->distance();
        $this->assertTrue(is_float($dis0));
        $this->assertEquals(3, $dis0);
    }

    /**
     * testDistance
     * @covers App\Component\Math\Graph\Path\Weight::distance
     */
    public function testDistance()
    {
        $dis0 = $this->instance->distance();
        $this->assertTrue(is_float($dis0));
    }

    /**
     * testMin
     * @covers App\Component\Math\Graph\Path\Weight::init
     * @covers App\Component\Math\Graph\Path\Weight::min
     */
    public function testMin()
    {
        $init = self::getMethod('init')->invokeArgs(
            $this->instance,
            ['a', 'c']
        );
        $this->assertTrue($init instanceof Weight);
        $min = self::getMethod('min')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($min));
    }
}
