<?php

namespace Tests\Component\Http;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Http\Session;

/**
 * @covers \App\Component\Http\Session::<public>
 */
class SessionTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Session
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
        $this->instance = new Session();
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
     * @covers App\Component\Http\Session::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Session);
    }

    /**
     * testSetHasGetSession
     * @covers App\Component\Http\Session::setSession
     * @covers App\Component\Http\Session::hasSession
     * @covers App\Component\Http\Session::getSession
     */
    public function testSetHasGetSession()
    {
        $n = 'name';
        $k = 'key';
        $v = 'value';
        $r = $this->instance->setSession($n, $v);
        $this->assertTrue($r instanceof Session);
        $this->assertTrue($this->instance->hasSession($n));
        $this->assertEquals($this->instance->getSession($n), $v);
        $r = $this->instance->setSession($n, $v, $k);
        $this->assertTrue($r instanceof Session);
        $this->assertTrue($this->instance->hasSession($n, $k));
        $this->assertEquals($this->instance->getSession($n, $k), $v);
    }

    /**
     * testDeleteSession
     * @covers App\Component\Http\Session::setSession
     * @covers App\Component\Http\Session::deleteSession
     * @covers App\Component\Http\Session::getSession
     */
    public function testDeleteSession()
    {
        $n = 'name';
        $k = 'key';
        $v = 'value';

        $rs = $this->instance->setSession($n, $v);
        $this->assertTrue($rs instanceof Session);
        $rg = $this->instance->getSession($n);
        $this->assertEquals($rg, $v);
        $rd = $this->instance->deleteSession($n);
        $this->assertTrue($rd instanceof Session);
        $rg = $this->instance->getSession($n);
        $this->assertEquals($rg, '');
        $rs = $this->instance->setSession($n, $v, $k);
        $this->assertTrue($rs instanceof Session);
        $rg = $this->instance->getSession($n, $k);
        $this->assertEquals($rg, $v);
        $rd = $this->instance->deleteSession($n, $k);
        $this->assertTrue($rd instanceof Session);
        $rg = $this->instance->getSession($n, $k);
        $this->assertEquals($rg, '');
    }
}
