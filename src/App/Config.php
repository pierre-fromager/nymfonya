<?php

namespace App;

use App\Component\Config as configComponent;

/**
 * App\Config
 *
 * is app config
 *
 * @author pierrefromager
 */

class Config extends configComponent
{

    /**
     * instanciate config
     *
     * @param string $env
     * @param string $path
     */
    public function __construct(string $env, string $path)
    {
        parent::__construct($env, $path);
        return $this;
    }
}
