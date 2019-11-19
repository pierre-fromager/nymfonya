<?php

namespace Tests\Component\Http;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Http\Request;
use App\Component\Http\Route;
use App\Component\Http\Routes;
use App\Component\Http\Router;

/**
 * @covers \App\Component\Http\Router::<public>
 */
class RouterTest extends PFT
{

    const TEST_ENABLE = true;
    const MATCH_ALL = ['/.*$/'];

    /**
     * instance
     *
     * @var RouterTest
     */
    protected $instance;

    /**
     * routes from config routes
     *
     * @return array
     */
    protected function routesConfig(): array
    {
        return [
            '/^(api\/v1\/auth)\/(.*)$/',
            '/^(config)\/(help)$/',
            '/^(config)\/(keygen)$/',
        ];
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->instance = new Router(
            new Routes($this->routesConfig()),
            new Request()
        );
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
     * @covers App\Component\Http\Router::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Router);
    }

    /**
     * testSetRoutes
     * @covers App\Component\Http\Router::setRoutes
     */
    public function testSetRoutes()
    {
        $this->assertTrue(
            $this->instance->setRoutes(
                new Routes(self::MATCH_ALL)
            ) instanceof Router
        );
    }

    /**
     * testGetSetParams
     * @covers App\Component\Http\Router::getParams
     * @covers App\Component\Http\Router::setParams
     */
    public function testGetSetParams()
    {
        $params0 = $this->instance->getParams();
        $this->assertTrue(is_array($params0));
        $this->assertEmpty($params0);
        $routeSlugged = new Route(
            'GET;/^(api\/v1\/restful)\/(\d+)$/;,id'
        );
        $matches = [
            '',
            100
        ];
        $rsp = $this->instance->setParams($routeSlugged, $matches);
        $this->assertTrue($rsp instanceof Router);
        $params1 = $this->instance->getParams();
        $this->assertEquals(['id' => 100], $params1);
    }

    /**
     * testGetMatchingRoute
     * @covers App\Component\Http\Router::getMatchingRoute
     */
    public function testGetMatchingRoute()
    {
        $mar = $this->instance->getMatchingRoute();
        $this->assertTrue(is_string($mar));
        $this->assertEmpty($mar);
    }

    /**
     * testCompile
     * @covers App\Component\Http\Router::compile
     */
    public function testCompile()
    {
        $comp0 = $this->instance->compile();
        $this->assertTrue(is_array($comp0));
        $this->assertEmpty($comp0);
        $this->instance->setRoutes(new Routes(self::MATCH_ALL));
        $comp1 = $this->instance->compile();
        $this->assertTrue(is_array($comp1));
    }
}
