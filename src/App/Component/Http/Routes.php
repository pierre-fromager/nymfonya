<?php

namespace App\Component\Http;

use App\Component\Http\Interfaces\IRoutes;
use App\Component\Http\Route;

class Routes implements IRoutes
{

    /**
     * routes config collection
     *
     * @var array
     */
    private $routesConfig = [];

    /**
     * route list as array
     *
     * @var array
     */
    private $routes = [];

    /**
     * __construct
     *
     * @param array $routesConfig
     * @return Routes
     */
    public function __construct(array $routesConfig = [])
    {
        if (!empty($routesConfig)) {
            $this->set($routesConfig);
        }
        return $this;
    }

    /**
     * returns routes as array
     *
     * @return array
     */
    public function get(): array
    {
        return $this->routes;
    }

    /**
     * returns routes as array
     *
     * @return array
     */
    public function getExpr(): array
    {
        $patterns = array_map(
            function (Route $i) {
                return $i->getExpr();
            },
            $this->routes
        );
        return $patterns;
    }

    /**
     * set routes as array and stack Route collection
     *
     * @param array $routesConfig
     * @return Routes
     */
    public function set(array $routesConfig): Routes
    {
        $this->routes = [];
        $this->prepare($routesConfig);
        $this->validate();
        return $this;
    }

    /**
     * stacks routes as Route object collection from routes config
     *
     * @param array $routesConfig
     * @return Routes
     */
    protected function prepare(array $routesConfig):Routes
    {
        $count = count($routesConfig);
        for ($c = 0; $c < $count; $c++) {
            $this->routes[] = new Route($routesConfig[$c]);
        }
        return $this;
    }

    /**
     * validate routes to be an array of regexp string
     *
     * @throws Exception
     */
    protected function validate()
    {
        $count = count($this->routes);
        for ($c = 0; $c < $count; $c++) {
            $route = $this->routes[$c]->getExpr();
            if (@preg_match($route, null) === false) {
                throw new \Exception('Route invalid expr ' . $route);
            }
        }
    }
}
