<?php

namespace Tests\Component\Math\Graph\Path;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Math\Graph\Path\Floydwarshall;

/**
 * @covers App\Component\Math\Graph\Path\Floydwarshall::<public>
 */
class FloydwarshallTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Floydwarshall
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
        $matrix0 = [
            [0, 1, 0],
            [0, 0, 1],
            [0, 0, 0],
        ];
        $nodeNames = ['a', 'b', 'c'];
        $this->init($matrix0, $nodeNames);
    }

    /**
     * test init
     *
     * @param array $graph
     * @return void
     */
    protected function init(array $graph = [], array $nodeNames = [])
    {
        $this->instance = new Floydwarshall($graph, $nodeNames);
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Floydwarshall::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
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
     * @covers App\Component\Math\Graph\Path\Floydwarshall::__construct
     * @covers App\Component\Math\Graph\Path\Floydwarshall::reset
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Floydwarshall);
    }

    /**
     * testPopulate
     * @covers App\Component\Math\Graph\Path\Floydwarshall::populate
     */
    public function testPopulate()
    {
        $pop = self::getMethod('populate')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($pop instanceof Floydwarshall);
    }

    /**
     * testProcess
     * @covers App\Component\Math\Graph\Path\Floydwarshall::process
     * @covers App\Component\Math\Graph\Path\Floydwarshall::populate
     */
    public function testProcess()
    {
        $this->assertTrue($this->instance->process() instanceof Floydwarshall);
    }

    /**
     * testGetDistances
     * @covers App\Component\Math\Graph\Path\Floydwarshall::process
     * @covers App\Component\Math\Graph\Path\Floydwarshall::getDistances
     */
    public function testGetDistances()
    {
        $this->instance->process();
        $mdists = $this->instance->getDistances();
        $this->assertTrue(is_array($mdists));
        $this->assertNotEmpty($mdists);
        $expected = [
            [0, 1, 2],
            [Floydwarshall::INFINITE, 0, 1],
            [Floydwarshall::INFINITE, Floydwarshall::INFINITE, 0]
        ];
        $this->assertEquals($expected, $mdists);
    }

    /**
     * testGetPrecedence
     * @covers App\Component\Math\Graph\Path\Floydwarshall::process
     * @covers App\Component\Math\Graph\Path\Floydwarshall::getPrecedence
     */
    public function testGetPrecedence()
    {
        $this->instance->process();
        $preds = $this->instance->getPrecedence();
        $this->assertTrue(is_array($preds));
        $this->assertNotEmpty($preds);
        $expected = [
            [0, 0, 1],
            [1, 1, 1],
            [2, 2, 2]
        ];
        $this->assertEquals($expected, $preds);
    }
}
