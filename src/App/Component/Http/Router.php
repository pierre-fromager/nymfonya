<?php

namespace App\Component\Http;

use App\Component\Http\Interfaces\IRoutes;
use App\Component\Http\Interfaces\IRoute;
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
     * @param array $configRoutes
     * @param IRequest $request
     */
    public function __construct(IRoutes $routes, IRequest $request)
    {
        $this->routes = $routes->get();
        $this->request = $request;
        $this->activeRoute = '';
        $this->params = [];
        $this->matchingRoute = '';
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
            $route = $routes[$i];
            $matches = [];
            $pattern = $route->getExpr();
            $match = preg_match($pattern, $this->activeRoute, $matches);
            if ($match) {
                $this->matchingRoute = $pattern;
                array_shift($matches);
                $this->setParams($route, $matches);
                return $matches;
            }
        }
        return [];
    }

    /**
     * return slugs params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * set params from slugs
     *
     * @return Router
     */
    public function setParams(IRoute $route, array $matches): Router
    {
        $slugs = $route->getSlugs();
        $slugCount = count($slugs);
        for ($c = 0; $c < $slugCount; $c++) {
            $slug = $slugs[$c];
            if (false === empty($slug)) {
                $this->params[$slug] = $matches[$c];
            }
        }
        return $this;
    }

    /**
     * return matching regexp pattern
     *
     * @return string
     */
    public function getMatchingRoute(): string
    {
        return $this->matchingRoute;
    }
}
