<?php

namespace App\Component\Pubsub;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionParameter;
use App\Component\Pubsub\EventInterface;

class ClosureWrapper extends ListenerAbstract implements ListenerInterface
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
}
