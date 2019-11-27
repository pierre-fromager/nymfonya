<?php

namespace App\Component\Pubsub;

use Closure;
use Exception;

class Dispatcher implements DispatcherInterface
{

    /**
     * listener stack.
     * Struct : [resName][event][hash]
     *
     * @var array
     */
    protected $stack = array();

    /**
     * Subscribes the listener to the resource's events.
     * If $resName is *, 
     * then the listener will be dispatched when the specified event 
     * is fired.
     * If $event is *, 
     * then the listener will be dispatched 
     * for any dispatched event of the specified resource.
     * If $resName and $event is *, 
     * the listener will be dispatched 
     * for any dispatched event for any resource.
     *
     * @param ListenerInterface $listener
     * @param String $resName
     * @param Mixed $event
     * @return Dispatcher
     */
    public function subscribe(
        ListenerInterface $listener,
        $resName = self::ALL,
        $event = self::ALL
    ): DispatcherInterface {
        $hash = $this->hash($listener);
        $this->stack[$resName][$event][$hash] = $listener;
        return $this;
    }

    /**
     * Subscribes the listener to the resource's events.
     * If $resName is *, 
     * then the listener will be dispatched when the specified event 
     * is fired.
     * If $event is *, 
     * then the listener will be dispatched 
     * for any dispatched event of the specified resource.
     * If $resName and $event is *, 
     * the listener will be dispatched 
     * for any dispatched event for any resource.
     *
     * @param Closure $closure
     * @param String $resName
     * @param Mixed $event
     * @return Dispatcher
     */
    public function subscribeClosure(
        Closure $closure,
        $resName = self::ALL,
        $event = self::ALL
    ): DispatcherInterface {
        $listener = $this->closureToListener($closure);
        $hash = $this->hash($listener);
        $this->stack[$resName][$event][$hash] = $listener;
        return $this;
    }

    /**
     * Unsubscribes the listener from the resource's events
     *
     * @param ListenerInterface $listener
     * @param String $resName
     * @param Mixed $event
     * @return Dispatcher
     */
    public function unsubscribe(
        ListenerInterface $listener,
        $resName = self::ALL,
        $event = self::ALL
    ): DispatcherInterface {
        $hash = $this->hash($listener);
        unset($this->stack[$resName][$event][$hash]);
        return $this;
    }

    /**
     * Publishes an event to all the listeners 
     * listening to the specified event 
     * for the specified resource
     *
     * @param EventInterface $event
     * @return Dispatcher
     */
    public function publish(EventInterface $event): DispatcherInterface
    {
        $resName = $event->getResourceName();
        $eventName = $event->getEventName();
        $this
            ->dispatchAllHandlers($event)
            ->dispatchResourcedHandlers($resName, $event)
            ->dispatchResourcedEventedHandlers($resName, $eventName, $event);
        return $this;
    }

    /**
     * dispatch to all handlers the wildcard handlers
     *
     * @param EventInterface $event
     * @return void
     */
    protected function dispatchAllHandlers(
        EventInterface $event
    ): DispatcherInterface {
        if (isset($this->stack[self::ALL][self::ALL])) {
            foreach ($this->stack[self::ALL][self::ALL] as $listener) {
                $listener->publish($event);
            }
        }
        return $this;
    }

    /**
     * dispatch to handlers identified by resource name
     * despite the event
     *
     * @param string $resName
     * @param EventInterface $event
     * @return void
     */
    protected function dispatchResourcedHandlers(
        string $resName,
        EventInterface $event
    ): DispatcherInterface {
        if (isset($this->stack[$resName][self::ALL])) {
            foreach ($this->stack[$resName][self::ALL] as $listener) {
                $listener->publish($event);
            }
        }
        return $this;
    }

    /**
     * dispatch to handlers identified by resource name and event name
     *
     * @param string $resName
     * @param EventInterface $event
     * @return void
     */
    protected function dispatchResourcedEventedHandlers(
        string $resName,
        string $eventName,
        EventInterface $event
    ): DispatcherInterface {
        if (isset($this->stack[$resName][$eventName])) {
            foreach ($this->stack[$resName][$eventName] as $listener) {
                $listener->publish($event);
            }
        }
        return $this;
    }

    /**
     * return hash for a listener instance
     *
     * @param ListenerInterface $listener
     * @return string
     */
    protected function hash(ListenerInterface $listener): string
    {
        return spl_object_hash($listener);
    }

    /**
     * transform closure to listener
     *
     * @param Closure $closure
     * @return ListenerInterface
     */
    protected function closureToListener(Closure $closure): ListenerInterface
    {
        $listener = new class ($closure) implements ListenerInterface
        {
            /**
             * listener as closure
             *
             * @var Closure
             */
            protected $closure;

            /**
             * instanciate
             *
             * @param Closure $closure
             */
            public function __construct(Closure $closure)
            {
                if (func_num_args($closure) === 0) {
                    throw new Exception(
                        self::ERR_CLOSURE_ARG_MISSING
                    );
                }
                $this->closure = $closure;
            }

            /**
             * publish
             *
             * @param EventInterface $event
             * @return void
             */
            public function publish(EventInterface $event)
            {
                call_user_func($this->closure, $event);
            }
        };
        return $listener;
    }
}
