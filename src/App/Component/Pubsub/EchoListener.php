<?php

namespace App\Component\Pubsub;

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
