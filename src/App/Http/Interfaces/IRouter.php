<?php

namespace App\Http\Interfaces;

use App\Http\Interfaces\IRequest;
use App\Http\Interfaces\IRoutes;

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
   * compiles routes
   *
   * @return void
   */
    public function compile();
}
