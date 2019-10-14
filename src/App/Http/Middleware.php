<?php

namespace App\Http;

use InvalidArgumentException;
use Closure;
use App\Container;
use App\Http\Interfaces\Middleware\ILayer;

/**
 * App\Http\Middleware
 *
 * is a copy/paste from onion team.
 *
 * @see https://github.com/esbenp/onion
 * @todo Thanks onion team, but composer install fails.
 */
class Middleware
{

    protected static $excMsg =  ' is not a valid onion layer.';
    protected $layers;

    /**
     * instanciate
     *
     * @param array $layers
     */
    public function __construct(array $layers = [])
    {
        $this->layers = $layers;
    }

    /**
     * Add layer(s) or Middleware
     *
     * @param  mixed $layers
     * @return Middleware
     */
    public function layer($layers)
    {
        if ($layers instanceof Middleware) {
            $layers = $layers->toArray();
        }
        if ($layers instanceof ILayer) {
            $layers = [$layers];
        }
        if (!is_array($layers)) {
            throw new \InvalidArgumentException(
                get_class($layers) . self::$excMsg
            );
        }
        return new static(array_merge($this->layers, $layers));
    }

    /**
     * Run middleware around core function and pass an
     * object through it
     *
     * @param  Container $container
     * @param  Closure $core
     * @return mixed
     */
    public function peel(Container $object, Closure $core)
    {
        $coreFunction = $this->createCoreFunction($core);
        $layers = array_reverse($this->layers);
        $completeMiddleware = array_reduce(
            $layers,
            function ($nextLayer, $layer) {
                return $this->createLayer($nextLayer, $layer);
            },
            $coreFunction
        );
        return $completeMiddleware($object);
    }

    /**
     * Get the layers of this onion,
     * can be used to merge with another onion
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->layers;
    }

    /**
     * The inner function of the onion.
     * this will be wrapped on layers
     *
     * @param  Closure $core the core function
     * @return Closure
     */
    protected function createCoreFunction(Closure $core): Closure
    {
        return function ($object) use ($core) {
            return $core($object);
        };
    }

    /**
     * Get an onion layer function.
     * we get the object from a previous layer and pass it inwards
     *
     * @param  Closure $nextLayer
     * @param  ILayer $layer
     * @return Closure
     */
    protected function createLayer(Closure $nextLayer, ILayer $layer): Closure
    {
        return function ($object) use ($nextLayer, $layer) {
            return $layer->peel($object, $nextLayer);
        };
    }
}
