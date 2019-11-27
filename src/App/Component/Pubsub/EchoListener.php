<?php

namespace App\Component\Pubsub;

class EchoListener implements ListenerInterface
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
