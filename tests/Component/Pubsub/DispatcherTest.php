<?php

namespace Tests\Component\Pubsub;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Pubsub\Dispatcher;
use App\Component\Pubsub\EchoListener;
use App\Component\Pubsub\EventInterface;

/**
 * @covers \App\Component\Pubsub\Dispatcher::<public>
 */
class DispatcherTest extends PFT
{

    const TEST_ENABLE = true;
    const RES_NAME = 'resname';
    const EVENT_NAME = 'eventname';

    /**
     * instance
     *
     * @var Dispatcher
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
        $this->instance = new Dispatcher();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->config = null;
        $this->container = null;
    }

    /**
     * testSubscribe
     * @covers App\Component\Pubsub\Dispatcher::subscribe
     */
    public function testSubscribe()
    {
        $sub = $this->instance->subscribe(
            new EchoListener(),
            self::RES_NAME,
            self::EVENT_NAME
        );
        $this->assertTrue(is_string($sub));
        $this->assertNotEmpty($sub);
    }

    /**
     * testSubscribeClosure
     * @covers App\Component\Pubsub\Dispatcher::subscribeClosure
     */
    public function testSubscribeClosure()
    {
        $subclo = $this->instance->subscribeClosure(
            function (EventInterface $event) {
                echo $event->getEventName() . "\n";
                echo $event->getResourceName() . "\n";
            },
            self::RES_NAME,
            self::EVENT_NAME
        );
        $this->assertTrue(is_string($subclo));
        $this->assertNotEmpty($subclo);
    }
}
