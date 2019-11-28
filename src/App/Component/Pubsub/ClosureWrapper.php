<?php

namespace App\Component\Pubsub;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionParameter;
use App\Component\Pubsub\EventInterface;

/**
 * ClosureWrapper
 *
 * is a mediator pattern to let closure to comply ListenerInterface.
 */
class ClosureWrapper extends ListenerAbstract implements ListenerInterface
{
    const PHP_VER_REF = '7.1';
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
        if ($this->getArgTypeName($params[0]) !== EventInterface::class) {
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
     * @param Closure $closure
     * @return ReflectionParameter[]
     */
    protected function getClosureParameters(Closure $closure): array
    {
        $reflection = new ReflectionFunction($closure);
        return $reflection->getParameters();
    }

    /**
     * return type name for a ReflectionParameter arg
     *
     * @see https://www.php.net/manual/fr/class.reflectionparameter.php
     * @param ReflectionParameter $arg
     * @return string
     */
    protected function getArgTypeName(ReflectionParameter $arg): string
    {
        return (version_compare(phpversion(), self::PHP_VER_REF, '<'))
            ? (string) $arg->getType()
            : $arg->getType()->getName();
    }
}
