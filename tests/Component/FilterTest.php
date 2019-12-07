<?php

namespace Tests\Component;

use App\Component\Auth\Adapters\File;
use PHPUnit\Framework\TestCase as PFT;
use App\Component\Filter;

/**
 * @covers \App\Component\Filter::<public>
 */
class FilterTest extends PFT
{

    const TEST_ENABLE = true;
    const _PREPARE = 'prepare';

    /**
     * instance
     *
     * @var Filter
     */
    protected $instance;

    /**
     * private key
     *
     * @var string
     */
    protected $privKey;

    /**
     * public key
     *
     * @var string
     */
    protected $pubKey;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->init();
    }


    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Filter::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }


    /**
     * init instance
     *
     * @param array $datas
     * @param array $filters
     * @return void
     */
    protected function init(array $datas = [], array $filters = [])
    {
        $this->instance = new Filter($datas, $filters);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->config = null;
        $this->request = null;
    }

    /**
     * testInstance
     * @covers App\Component\Filter::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Filter);
    }

    /**
     * testProcess
     * @covers App\Component\Filter::process
     */
    public function testProcess()
    {
        $this->assertTrue(
            $this->instance->process() instanceof Filter
        );
    }

    /**
     * testToArray
     * @covers App\Component\Filter::toArray
     */
    public function testToArray()
    {
        $this->assertTrue(
            is_array($this->instance->toArray())
        );
    }

    /**
     * testPrepare
     * @covers App\Component\Filter::prepare
     * @covers App\Component\Filter::process
     * @covers App\Component\Filter::toArray
     */
    public function testPrepare()
    {

        $garbage = '\nXz+666\3+8@/n';
        $expected = '+6663+8';
        $expectedInt = 6663;
        $key = 'age';
        $rawIput = [$key => $garbage];
        $filters = [$key => \FILTER_SANITIZE_NUMBER_INT];
        $this->init($rawIput, $filters);
        $prp0 = self::getMethod(self::_PREPARE)->invokeArgs($this->instance, []);
        $this->assertTrue($prp0 instanceof Filter);
        $this->instance->process();
        $r0 = $this->instance->toArray();
        $this->assertTrue(is_array($r0));
        $this->assertTrue(isset($r0[$key]));
        $fa0 = $r0[$key];
        $this->assertEquals($fa0, $expected);
        $iv0 = (int) $fa0;
        $this->assertTrue(is_int($iv0));
        $this->assertEquals($iv0, $expectedInt);
        $myAgeFilter = new class
        {
            /**
             * custom filter process
             *
             * @param string $v
             * @return string
             */
            public function process(string $v): string
            {
                return filter_var($v, \FILTER_SANITIZE_NUMBER_INT);
            }
        };
        $filters = [$key => $myAgeFilter];
        $this->init($rawIput, $filters);
        $prp1 = self::getMethod(self::_PREPARE)->invokeArgs($this->instance, []);
        $this->instance->process();
        $r1 = $this->instance->toArray();
        $this->assertTrue(is_array($r1));
        $this->assertTrue(isset($r1[$key]));
        $fa1 = $r1[$key];
        $this->assertEquals($fa1, $expected);
        $iv1 = (int) $fa1;
        $this->assertTrue(is_int($iv1));
        $this->assertEquals($iv1, $expectedInt);
        $this->assertTrue($prp1 instanceof Filter);
    }
}
