<?php

namespace Tests\Component\Math\Graph\Path;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Math\Graph\Path\Floydwarshall;

/**
 * Running Floydwarshall method on square matrix.
 * Providing precedence and distance matrix.
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
     * @todo improve more usecases with various matrice inputs
     */
    protected function setUp(): void
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $weightsMatrix = [
            #a  b  c
            [0, 5, 0], # a
            [5, 0, 5], # b
            [0, 5, 0], # c
        ];
        $nodeNames = ['a', 'b', 'c'];
        $this->init($weightsMatrix, $nodeNames);
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
    protected function tearDown(): void
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
     * testSearchPath
     * @covers App\Component\Math\Graph\Path\Floydwarshall::process
     * @covers App\Component\Math\Graph\Path\Floydwarshall::searchPath
     */
    public function testSearchPath()
    {
        $this->instance->process();
        $spa = self::getMethod('searchPath')->invokeArgs(
            $this->instance,
            [0, 1]
        );
        $this->assertTrue($spa instanceof Floydwarshall);
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
            #a   b   c
            [00, 05, 10], # a
            [05, 00, 05], # b
            [10, 05, 00], # c
        ];
        $this->assertEquals($expected, $mdists);
    }

    /**
     * return node name from row integer
     * @covers App\Component\Math\Graph\Path\Floydwarshall::nodeName
     */
    public function testNodeName()
    {
        $non = $this->instance->nodeName(0);
        $this->assertTrue(is_string($non));
    }

    /**
     * testGetDistance
     * @covers App\Component\Math\Graph\Path\Floydwarshall::process
     * @covers App\Component\Math\Graph\Path\Floydwarshall::getDistance
     */
    public function testGetDistance()
    {
        $this->instance->process();
        $distAB = $this->instance->getDistance(0, 1);
        $this->assertTrue(is_float($distAB));
        $this->assertNotEmpty($distAB);
        $expected = 5;
        $distAC = $this->instance->getDistance(0, 2);
        $this->assertTrue(is_float($distAC));
        $this->assertNotEmpty($distAC);
        $expected = 10;
        $this->assertEquals($expected, $distAC);
        $distUnknown = $this->instance->getDistance(0, 10);
        $this->assertTrue(is_float($distUnknown));
        $this->assertNotEmpty($distUnknown);
        $this->assertEquals(Floydwarshall::INFINITE, $distUnknown);
    }

    /**
     * testGetPrecedences
     * @covers App\Component\Math\Graph\Path\Floydwarshall::process
     * @covers App\Component\Math\Graph\Path\Floydwarshall::getPrecedences
     */
    public function testGetPrecedences()
    {
        $this->instance->process();
        $preds = $this->instance->getPrecedences();
        $this->assertTrue(is_array($preds));
        $this->assertNotEmpty($preds);
        $expected = [
            #a  b  c
            [0, 0, 1], # a
            [1, 1, 1], # b
            [1, 2, 2], # c
        ];
        $this->assertEquals($expected, $preds);
    }

    /**
     * testPath
     * @covers App\Component\Math\Graph\Path\Floydwarshall::path
     * @covers App\Component\Math\Graph\Path\Floydwarshall::searchPath
     * @todo use data provider
     */
    public function testPath()
    {
        $this->instance->process();

        $path0 = $this->instance->path('a', 'c');
        $this->assertTrue(is_array($path0));
        $this->assertNotEmpty($path0);
        $expected0 = [0, 1, 2];
        $this->assertEquals($expected0, $path0);

        $path0n = $this->instance->path('a', 'c', true);
        $this->assertTrue(is_array($path0n));
        $this->assertNotEmpty($path0n);
        $expected0n = ['a', 'b', 'c'];
        $this->assertEquals($expected0n, $path0n);

        $path1 = $this->instance->path('c', 'a');
        $this->assertTrue(is_array($path1));
        $this->assertNotEmpty($path1);
        $expected1 = [2, 1, 0];
        $this->assertEquals($expected1, $path1);

        $path1n = $this->instance->path('c', 'a', true);
        $this->assertTrue(is_array($path1n));
        $this->assertNotEmpty($path1n);
        $expected1n = ['c', 'b', 'a'];
        $this->assertEquals($expected1n, $path1n);

        $path2 = $this->instance->path('b', 'a');
        $this->assertTrue(is_array($path2));
        $this->assertNotEmpty($path2);
        $expected = [1, 0];
        $this->assertEquals($expected, $path2);

        $path3 = $this->instance->path('a', 'b');
        $this->assertTrue(is_array($path3));
        $this->assertNotEmpty($path3);
        $expected = [0, 1];
        $this->assertEquals($expected, $path3);
    }
}
