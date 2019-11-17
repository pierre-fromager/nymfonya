<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Http\Request;
use App\Component\Http\Route;

/**
 * @covers \App\Component\Http\Route::<public>
 */
class RouteTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Route
     */
    protected $instance;

    /**
     * route as defined in config
     *
     * @var string
     */
    protected $routeConfig;

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
        $this->init();
    }

    /**
     * init
     *
     * @param boolean $withSemiColumn
     * @return void
     */
    protected function init(bool $withSemiColumn = false)
    {
        $this->routeConfig = ($withSemiColumn === false)
            ? '/^(config)\/(help)$/'
            : 'GET;/^(api\/v1\/restful)\/(\d+)$/;whatever';
        $this->instance = new Route($this->routeConfig);
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
        $class = new \ReflectionClass(Route::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Http\Route::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Route);
    }

    /**
     * testGetExpr
     * @covers App\Component\Http\Route::getExpr
     */
    public function testGetExpr()
    {
        $this->assertEquals($this->instance->getExpr(), $this->routeConfig);
    }

    /**
     * testGetExprAdvanced
     * @covers App\Component\Http\Route::getExpr
     */
    public function testGetExprAdvanced()
    {
        $this->init(true);
        $this->assertEquals(
            $this->instance->getMethod(),
            Request::METHOD_GET
        );
        $this->assertEquals(
            $this->instance->getExpr(),
            '/^(api\/v1\/restful)\/(\d+)$/'
        );
    }

    /**
     * testGetMethod
     * @covers App\Component\Http\Route::getMethod
     */
    public function testGetMethod()
    {
        $method = $this->instance->getMethod();
        $this->assertTrue(is_string($method));
        $this->assertNotEmpty($method);
        $this->assertEquals('GET', $method);
    }

    /**
     * testGetSlugs
     * @covers App\Component\Http\Route::getSlugs
     */
    public function testGetSlugs()
    {
        $slugs = $this->instance->getSlugs();
        $this->assertTrue(is_array($slugs));
        $this->assertEmpty($slugs);
    }

    /**
     * testParsedSlugs
     * @covers App\Component\Http\Route::parsedSlugs
     */
    public function testParsedSlugs()
    {
        $rawSlug0 = ',id';
        $slugs0 = self::getMethod('parsedSlugs')->invokeArgs(
            $this->instance,
            [$rawSlug0]
        );
        $this->assertTrue(is_array($slugs0));
        $this->assertEquals(['', 'id'], $slugs0);
        $rawSlug1 = '';
        $slugs1 = self::getMethod('parsedSlugs')->invokeArgs(
            $this->instance,
            [$rawSlug1]
        );
        $this->assertTrue(is_array($slugs1));
        $this->assertEmpty($slugs1);
    }
}
