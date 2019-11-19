<?php

namespace App;

use App\Component\Http\Kernel;

class BundleApi extends Kernel
{

    public function __construct(string $env, string $path)
    {
        parent::__construct($env, $path);
    }
}
