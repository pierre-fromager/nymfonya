<?php

namespace App\Component\Http;

use App\Component\Http\Interfaces\IRoutes;
use App\Component\Http\Interfaces\IRequest;
use App\Component\Http\Interfaces\IRouter;

class Router implements IRouter
{
    /**
     * active route
     *
     * @var string
     */
    private $activeRoute;

    /**
     * routes collection
     *
     * @var IRoutes
     */
    private $routes;

    /**
     * request
     *
     * @var IRequest
     */
    private $request = null;

    /**
     * route params
     *
     * @var array
     */
    private $params;

    /**
     * route match expr
     *
     * @var string
     */
    private $matchingRoute;

    /**
     * instanciate
     *
     * @param IRoutes $routes
     * @param IRequest $request
     */
    public function __construct(IRoutes $routes, IRequest $request)
    {
        $this->routes = [];
        $this->request = $request;
        $this->activeRoute = '';
        $this->params = [];
        $this->matchingRoute = '';
        $this->setRoutes($routes);
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
        $this->routes = $routes->get();
        return $this;
    }

    /**
     * compile
     *
     * @return array
     */
    public function compile(): array
    {
        
        $routes = $this->routes;
        $routesLength = count($routes);
        for ($i = 0; $i < $routesLength; $i++) {
            $matches = [];
            $match = preg_match(
                $routes[$i]->getExpr(),
                $this->activeRoute,
                $matches
            );
            if ($match) {
                $this->params = $matches;
                $this->matchingRoute = $routes[$i]->getExpr();
                array_shift($matches);
                return $matches;
            }
        }
        return [];
    }

    public function getParams():array
    {
        return $this->params;
    }

    public function getMatchingRoute():string
    {
        return $this->matchingRoute;
    }
}
