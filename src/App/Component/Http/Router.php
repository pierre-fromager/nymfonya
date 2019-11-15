<?php

namespace App\Component\Http;

use App\Component\Http\Interfaces\IRoutes;
use App\Component\Http\Interfaces\IRequest;
use App\Component\Http\Interfaces\IRouter;

class Router implements IRouter
{
    private $activeRoute = '';
    private $routes = [];
    private $request = null;

    /**
     * instanciate
     *
     * @param IRoutes $routes
     * @param IRequest $request
     */
    public function __construct(IRoutes $routes, IRequest $request)
    {
        $this->setRoutes($routes);
        $this->request = $request;
        $this->activeRoute = substr($this->request->getUri(), 1);
        return $this;
    }

    /**
     * set routes
     *
     * @param IRoutes $routes
     * @return Router
     */
    public function setRoutes(IRoutes $routes): Router
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * compile
     *
     * @return array
     */
    public function compile(): array
    {
        $routes = $this->routes->get();
        $routesLength = count($routes);
        for ($i = 0; $i < $routesLength; $i++) {
            $matches = [];
            $match = preg_match($routes[$i], $this->activeRoute, $matches);
            if ($match) {
                array_shift($matches);
                return $matches;
            }
        }
        return [];
    }
}
