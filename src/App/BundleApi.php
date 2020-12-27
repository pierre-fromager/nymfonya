<?php

declare(strict_types=1);

namespace App;

//use App\Component\Http\Kernel;
use Nymfonya\Component\Http\Kernel;

class BundleApi extends Kernel
{

    public function __construct(string $env, string $path)
    {
        parent::__construct($env, $path);
    }
}
