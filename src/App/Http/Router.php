<?php

namespace App\Http;

use App\Http\Interfaces\IRoutes;
use App\Http\Interfaces\IRequest;
use App\Http\Interfaces\IRouter;

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
        $this->routes = $routes;
        $this->request = $request;
        $this->activeRoute = substr($this->request->getUri(), 1);
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
        //echo json_encode($routes);
        $routesLength = count($routes);
        for ($i = 0; $i < $routesLength; $i++) {
            $matches = [];
            $match = preg_match($routes[$i], $this->activeRoute, $matches);
            //echo json_encode($matches);
            if ($matches) {
                //echo json_encode($matches);die;
            }
            //var_dump($matches);die;
            if ($match) {
                array_shift($matches);
                return $matches;
            }
        }
        return [];
    }
}
