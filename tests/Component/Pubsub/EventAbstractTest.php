<?php

namespace Tests\Component\Pubsub;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Pubsub\Event;
use App\Component\Pubsub\EventAbstract;
use App\Component\Pubsub\EventInterface;
use stdClass;

/**
 * @covers \App\Component\Pubsub\EventAbstract::<public>
 */
class EventAbstractTest extends PFT
{

    const TEST_ENABLE = true;
    const RES_NAME = 'resname';
    const EVENT_NAME = 'eventname';

    /**
     * instance
     *
     * @var EventAbstract
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
        $datas = new stdClass;
        $this->instance = new class (
            self::RES_NAME,
            self::EVENT_NAME,
            $datas
        ) extends EventAbstract implements EventInterface
        {
        };
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
     * @covers App\Component\Pubsub\EventAbstract::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof EventAbstract);
        $this->assertTrue(
            method_exists($this->instance, 'getEventName')
        );
        $this->assertTrue(
            method_exists($this->instance, 'getResourceName')
        );
        $this->assertTrue(
            method_exists($this->instance, 'getDatas')
        );
        $implements = class_implements(Event::class, true);
        $this->assertTrue(
            in_array(EventInterface::class, $implements)
        );
    }

    /**
     * testGetEventName
     * @covers App\Component\Pubsub\EventAbstract::getEventName
     */
    public function testGetEventName()
    {
        $gen = $this->instance->getEventName();
        $this->assertTrue(is_string($gen));
        $this->assertNotEmpty($gen);
    }

    /**
     * testGetResourceName
     * @covers App\Component\Pubsub\EventAbstract::getResourceName
     */
    public function testGetResourceName()
    {
        $grn = $this->instance->getResourceName();
        $this->assertTrue(is_string($grn));
        $this->assertNotEmpty($grn);
    }

    /**
     * testGetDatas
     * @covers App\Component\Pubsub\EventAbstract::getDatas
     */
    public function testGetDatas()
    {
        $grn = $this->instance->getDatas();
        $this->assertNotEmpty($grn);
    }
}
