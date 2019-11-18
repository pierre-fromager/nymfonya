<?php

namespace App\Component\Http\Interfaces;

use App\Component\Http\Interfaces\IRequest;
use App\Component\Http\Interfaces\IRoutes;
use App\Component\Http\Router;

interface IRouter
{
    const URI_SEPARATOR = '/';
    const REQUEST_URI = 'REQUEST_URI';

    /**
     * instanciate
     *
     * @param array $routes
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

    /**
     * return slugs params
     *
     * @return array
     */
    public function getParams(): array;

    /**
     * set params from slugs
     *
     * @return Router
     */
    public function setParams(IRoute $route, array $matches): Router;

    /**
     * return matching regexp pattern
     *
     * @return string
     */
    public function getMatchingRoute(): string;
}
