<?php

namespace App\Component\Pubsub;

use App\Component\Pubsub\EventInterface;

interface ListenerInterface
{
    /**
     * Accepts an event and does something with it
     *
     * @param EventInterface $event
     */
    public function publish(EventInterface $event);
}
