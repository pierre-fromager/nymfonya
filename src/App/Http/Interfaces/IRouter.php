<?php

namespace App\Http\Interfaces;

use App\Http\Interfaces\IRequest;
use App\Http\Interfaces\IRoutes;
use App\Http\Router;

interface IRouter
{
    const URI_SEPARATOR = '/';
    const REQUEST_URI = 'REQUEST_URI';

    /**
     * instanciate
     *
     * @param IRoutes $routes
     * @param IRequest $request
     */
    public function __construct(IRoutes $routes, IRequest $request);

    /**
     * assign routes to router
     *
     * @param IRoutes $routes
     * @return Router
     */
    public function setRoutes(IRoutes $routes): Router;

    /**
     * compiles routes
     *
     * @return array
     */
    public function compile(): array;
}
