<?php

namespace App\Component\Pubsub;

class EventAbstract implements EventInterface
{
    /**
     * The name of the resource publishing this event
     * @var string
     */
    protected $resourceName;

    /**
     * The name of this event
     * @var string
     */
    protected $eventName;

    /**
     * Any data associated with this event
     * @var mixed
     */
    protected $data;

    /**
     * @param string $resourceName  name of the publisher
     * @param string $eventName     name of the event
     * @param mixed $data           [OPTIONAL] Additional event data
     */
    public function __construct(
        string $resourceName,
        string $eventName,
        $data = null
    ) {
        $this->resourceName = $resourceName;
        $this->eventName = $eventName;
        $this->data = $data;
    }

    /**
     * return the name of the event
     *
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * return the name of the resource
     *
     * @return string
     */
    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    /**
     * return datas
     *
     * @return mixed
     */
    public function getDatas()
    {
        return $this->datas;
    }
}
