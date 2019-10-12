<?php

namespace App\Http;

use App\Http\Interfaces\IRoutes;

class Routes implements IRoutes
{
    /**
     * route list as array
     *
     * @var array
     */
    private $routes = [];

    /**
     * __construct
     *
     * @param array $routes
     * @return Routes
     */
    public function __construct(array $routes = [])
    {
        if ($routes) {
            $this->set($routes);
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
     * set routes as array and returns Routes
     *
     * @param array $routes
     * @return Routes
     */
    public function set(array $routes): Routes
    {
        $this->routes = $routes;
        return $this;
    }
}
