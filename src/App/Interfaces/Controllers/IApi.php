<?php

namespace App\Interfaces\Controllers;

use App\Container;

interface IApi
{

    const USER_AGENT = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13';
    const BUFFER_SIZE = 10485764;

    public function __construct(Container $container);

    public function preflight(): IApi;
}
