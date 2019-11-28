<?php

namespace Tests\Component\Pubsub;

use App\Component\Pubsub\ListenerAbstract;
use App\Component\Pubsub\ListenerInterface;
use App\Component\Pubsub\EventInterface;

/**
 * dumb echo listener
 */
class EchoListener extends ListenerAbstract implements ListenerInterface
{

    /**
     * publish
     *
     * @param EventInterface $event
     * @return void
     */
    public function publish(EventInterface $event)
    {
        echo "{$event->getResourceName()} published a {$event->getEventName()}\n";
    }
}
