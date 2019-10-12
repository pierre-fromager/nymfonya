<?php

namespace App\Http\Interfaces;

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
     * @return void
     */
    public function set(array $routes);
}
