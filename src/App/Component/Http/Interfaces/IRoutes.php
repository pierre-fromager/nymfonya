<?php

namespace App\Component\Http\Interfaces;

use App\Component\Http\Routes;

interface IRoutes
{
    /**
     * instanciate
     *
     * @param array $routes
     */
    public function __construct(array $routes);

    /**
     * Undocumented function
     *
     * @return array
     */
    public function get(): array;

    /**
     * set routes from array
     *
     * @param array $routes
     * @return Routes
     */
    public function set(array $routes): Routes;
}
