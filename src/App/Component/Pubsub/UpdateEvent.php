<?php

namespace App\Component\Pubsub;

class UpdateEvent extends Event
{
    public $eventName = 'Update';

    /**
     * instanciate
     *
     * @param string $resourceName
     * @param Mixed $data
     */
    public function __construct(string $resourceName, $data=null)
    {
        parent::__construct($resourceName, get_class($this), $data);
    }
}
