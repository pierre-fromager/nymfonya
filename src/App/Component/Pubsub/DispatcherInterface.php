<?php

namespace App\Component\Pubsub;

use App\Component\Pubsub\ListenerInterface;
use App\Component\Pubsub\EventInterface;

interface DispatcherInterface
{

    const ANY = '*';
   
    /**
     * subscribe
     *
     * @param ListenerInterface $listener
     * @param string $resourceName
     * @param string $event
     * @return string
     */
    public function subscribe(
        ListenerInterface $listener,
        $resourceName = self::ANY,
        $event = self::ANY
    ): string;

    /**
     * unsubscribe
     *
     * @param ListenerInterface $listener
     * @param string $resourceName
     * @param string $event
     * @return boolean
     */
    public function unsubscribe(
        ListenerInterface $listener,
        $resourceName = self::ANY,
        $event = self::ANY
    ): bool;

    /**
     * publish
     *
     * @param EventInterface $event
     * @return Dispatcher
     */
    public function publish(EventInterface $event): DispatcherInterface;
}
