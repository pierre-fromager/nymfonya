<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Http\Request;
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
     * regexp collection
     *
     * @return array
     */
    protected function routesRegexp(): array
    {
        return [
            '/^(api\/v1\/auth)\/(.*)$/',
            '/^(config)\/(help)$/',
            '/^(config)\/(keygen)$/'
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
            new Routes($this->routesRegexp()),
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
        $routesAny = new Routes(self::MATCH_ALL);
        $this->assertTrue(
            $this->instance->setRoutes(
                $routesAny
            ) instanceof Router
        );
    }

    /**
     * testCompile
     * @covers App\Component\Http\Router::compile
     */
    public function testCompile()
    {
        $this->assertTrue(is_array($this->instance->compile()));
        $routesAny = new Routes(self::MATCH_ALL);
        $this->instance->setRoutes($routesAny);
        $this->assertTrue(is_array($this->instance->compile()));
    }
}
