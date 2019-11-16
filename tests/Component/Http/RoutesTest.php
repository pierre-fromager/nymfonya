<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Http\Routes;

/**
 * @covers \App\Component\Http\Routes::<public>
 */
class RoutesTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Routes
     */
    protected $instance;

    /**
     * rexexp string collection
     *
     * @var array
     */
    protected $regexRoutes;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->regexRoutes = ['/^(config)\/(help)$/'];
        $this->instance = new Routes($this->regexRoutes);
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
     * @covers App\Component\Http\Routes::__construct
     * @covers App\Component\Http\Routes::getExpr
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Routes);
        $this->assertEquals($this->instance->getExpr(), $this->regexRoutes);
    }

    /**
     * testGetSet
     * @covers App\Component\Http\Routes::set
     * @covers App\Component\Http\Routes::getExpr
     */
    public function testGetSet()
    {
        $routesArray = [
            '/^(api\/v1\/auth)\/(.*)$/',
            '/^(config)\/(keygen)$/'
        ];
        $this->instance->set($routesArray);
        $this->assertEquals($this->instance->getExpr(), $routesArray);
    }

    /**
     * testValidate
     * @covers App\Component\Http\Routes::validate
     */
    public function testValidate()
    {
        self::getMethod('validate')->invokeArgs(
            $this->instance,
            []
        );
        $routesArray = [
            '/^(api\/v1\/auth)\/(.*)$/',
            '/^(config)\/(keygen)$/'
        ];
        $this->instance->set($routesArray);
        $this->assertTrue($this->instance instanceof Routes);
    }

    /**
     * testValidateException
     * @covers App\Component\Http\Routes::set
     * @covers App\Component\Http\Routes::validate
     */
    public function testValidateException()
    {
        $this->expectException(\Exception::class);
        $this->instance->set(['crappyRegexp']);
    }
}
