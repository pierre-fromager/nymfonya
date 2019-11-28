<?php

namespace App\Component\Pubsub;

use App\Component\Pubsub\ListenerInterface;
use App\Component\Pubsub\EventInterface;

interface DispatcherInterface
{

    const ALL = '*';
   
    /**
     * subscribe
     *
     * @param ListenerInterface $listener
     * @param string $resourceName
     * @param string $event
     * @return DispatcherInterface
     */
    public function subscribe(
        ListenerInterface $listener,
        $resourceName = self::ALL,
        $event = self::ALL
    ): DispatcherInterface;

    /**
     * unsubscribe
     *
     * @param ListenerInterface $listener
     * @param string $resourceName
     * @param string $event
     * @return DispatcherInterface
     */
    public function unsubscribe(
        ListenerInterface $listener,
        $resourceName = self::ALL,
        $event = self::ALL
    ): DispatcherInterface;

    /**
     * publish
     *
     * @param EventInterface $event
     * @return Dispatcher
     */
    public function publish(EventInterface $event): DispatcherInterface;
}
