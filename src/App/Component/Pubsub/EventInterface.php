<?php

namespace App\Component\Pubsub;

interface EventInterface
{

    /**
     * instanciate
     *
     * @param string $resourceName  name of the publisher
     * @param string $eventName     name of the event
     * @param mixed $data           [OPTIONAL] Additional event data
     */
    public function __construct(string $resourceName, string $eventName, $data = null);

    /**
     * return the name of the event
     *
     * @return string
     */
    public function getEventName(): string;

    /**
     * return the name of the resource
     *
     * @return string
     */
    public function getResourceName(): string;
}
