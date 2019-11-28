<?php

namespace App\Component\Pubsub;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionParameter;
use App\Component\Pubsub\EventInterface;

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
     * @return string
     */
    public function subscribe(
        ListenerInterface $listener,
        $resName = self::ANY,
        $event = self::ANY
    ): string {
        $hash = $listener->hash();
        $this->stack[$resName][$event][$hash] = $listener;
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
     * @param Mixed $event
     * @return string
     */
    public function subscribeClosure(
        Closure $closure,
        $resName = self::ANY,
        $eventName = self::ANY
    ): string {
        $listener = $this->closureToListener($closure);
        $hash = $listener->hash();
        $this->stack[$resName][$eventName][$hash] = $listener;
        return $hash;
    }

    /**
     * Unsubscribes the listener from the resource's events
     *
     * @param string $hash
     * @param String $resName
     * @param Mixed $event
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
     * @return void
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
     * transform closure to listener
     *
     * @param Closure $closure
     * @return ListenerInterface
     */
    protected function closureToListener(Closure $closure): ListenerInterface
    {
        $listener = new class ($closure) extends ListenerAbstract implements ListenerInterface
        {
            const ERR_CLOSURE_ARG_MISSING = 'Listener closure required at least one Event argument';
            const ERR_CLOSURE_ARG_INVALID = 'Listener closure arg type should comply EventInterface';

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
                $params = $this->getClosureParameters($closure);
                if (count($params) === 0) {
                    throw new Exception(self::ERR_CLOSURE_ARG_MISSING);
                }
                $argTypeName = (version_compare(phpversion(), '7.1', '<'))
                    ? (string) $params[0]->getType()
                    : $params[0]->getType()->getName();
                if ($argTypeName !== EventInterface::class) {
                    throw new Exception(self::ERR_CLOSURE_ARG_INVALID);
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

            /**
             * return an array of reflexion parameter
             *
             * @see https://www.php.net/manual/fr/class.reflectionparameter.php
             *
             * @param Closure $closure
             * @return ReflectionParameter[]
             */
            protected function getClosureParameters(Closure $closure): array
            {
                $reflection = new ReflectionFunction($closure);
                return $reflection->getParameters();
            }
        };
        return $listener;
    }
}
