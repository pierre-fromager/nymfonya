<?php

namespace Tests\Component\Pubsub;

use Exception;
use PHPUnit\Framework\TestCase as PFT;
use App\Component\Pubsub\Event;
use App\Component\Pubsub\EventInterface;
use App\Component\Pubsub\ClosureWrapper;
use App\Component\Pubsub\ListenerInterface;
use App\Component\Pubsub\ListenerAbstract;
use stdClass;

/**
 * @covers \App\Component\Pubsub\ClosureWrapper::<public>
 */
class ClosureWrapperTest extends PFT
{

    const TEST_ENABLE = true;
    const _STATE = 'state';
    const _DONE = 'done';
    const _RESNAME = 'resname';
    const _EVTNAME = 'evtname';

    /**
     * instance
     *
     * @var ClosureWrapper
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
        $this->instance = new ClosureWrapper(
            function (EventInterface $event) {
                // do nothing here
            }
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
     * @covers App\Component\Pubsub\ClosureWrapper::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof ListenerAbstract);
        $implements = class_implements(ListenerAbstract::class, true);
        $this->assertTrue(
            in_array(ListenerInterface::class, $implements)
        );
    }

    /**
     * testInstanceExceptionMissing
     * @covers App\Component\Pubsub\ClosureWrapper::__construct
     */
    public function testInstanceExceptionMissing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            ClosureWrapper::ERR_CLOSURE_ARG_MISSING
        );
        $this->instance = new ClosureWrapper(
            function () {
            }
        );
    }

    /**
     * testInstanceExceptionInvalid
     * @covers App\Component\Pubsub\ClosureWrapper::__construct
     */
    public function testInstanceExceptionInvalid()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            ClosureWrapper::ERR_CLOSURE_ARG_INVALID
        );
        $this->instance = new ClosureWrapper(
            function (string $event) {
            }
        );
    }

    /**
     * testPublish
     * @covers App\Component\Pubsub\ClosureWrapper::publish
     */
    public function testPublish()
    {
        $stateClosure = function (EventInterface $event) {
            $eventDatas = $event->getDatas();
            $eventDatas->state = self::_DONE;
            $eventDatas->{self::_RESNAME} = $event->getResourceName();
            $eventDatas->{self::_EVTNAME} = $event->getEventName();
        };
        $datas = new stdClass();
        $this->assertObjectNotHasAttribute(self::_STATE, $datas);
        $this->assertObjectNotHasAttribute(self::_RESNAME, $datas);
        $this->assertObjectNotHasAttribute(self::_EVTNAME, $datas);
        $this->instance = new ClosureWrapper($stateClosure);
        $event = new Event(self::_RESNAME, self::_EVTNAME, $datas);
        $this->instance->publish($event);
        $this->assertObjectHasAttribute(self::_STATE, $datas);
        $this->assertTrue(is_string($datas->state));
        $this->assertEquals(self::_DONE, $datas->state);
        $this->assertObjectHasAttribute(self::_RESNAME, $datas);
        $this->assertTrue(is_string($datas->{self::_RESNAME}));
        $this->assertEquals(self::_RESNAME, $datas->{self::_RESNAME});
        $this->assertObjectHasAttribute(self::_EVTNAME, $datas);
        $this->assertTrue(is_string($datas->{self::_EVTNAME}));
        $this->assertEquals(self::_EVTNAME, $datas->{self::_EVTNAME});
    }
}
