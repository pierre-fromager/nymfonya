<?php

namespace App\Component\Pubsub;

use Closure;
use App\Component\Pubsub\EventInterface;
use App\Component\Pubsub\ClosureWrapper;

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
     * @param String $eventName
     * @return string
     */
    public function subscribe(
        ListenerInterface $listener,
        $resName = self::ANY,
        $eventName = self::ANY
    ): string {
        $hash = $listener->hash();
        $this->stack[$resName][$eventName][$hash] = $listener;
        return $hash;
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
     * @param String $eventName
     * @return string
     */
    public function subscribeClosure(
        Closure $closure,
        $resName = self::ANY,
        $eventName = self::ANY
    ): string {
        $listener = new ClosureWrapper($closure);
        $hash = $listener->hash();
        $this->stack[$resName][$eventName][$hash] = $listener;
        return $hash;
    }

    /**
     * Unsubscribes the listener from the resource's events
     *
     * @param string $hash
     * @param String $resName
     * @param String $eventName
     * @return boolean
     */
    public function unsubscribe(
        string $hash,
        $resName = self::ANY,
        $eventName = self::ANY
    ): bool {
        if (isset($this->stack[$resName][$eventName][$hash])) {
            unset($this->stack[$resName][$eventName][$hash]);
            return true;
        }
        return false;
    }

    /**
     * Publishes an event to all the listeners
     * listening to the specified event
     * for the specified resource
     *
     * @param EventInterface $event
     * @return DispatcherInterface
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
     * @return DispatcherInterface
     */
    protected function dispatchAllHandlers(
        EventInterface $event
    ): DispatcherInterface {
        if (isset($this->stack[self::ANY][self::ANY])) {
            foreach ($this->stack[self::ANY][self::ANY] as $listener) {
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
     * @return DispatcherInterface
     */
    protected function dispatchResourcedHandlers(
        string $resName,
        EventInterface $event
    ): DispatcherInterface {
        if (isset($this->stack[$resName][self::ANY])) {
            foreach ($this->stack[$resName][self::ANY] as $listener) {
                $listener->publish($event);
            }
        }
        return $this;
    }

     /**
      * dispatch to handlers identified by resource name and event name
      *
      * @param string $resName
      * @param string $eventName
      * @param EventInterface $event
      * @return DispatcherInterface
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
}
