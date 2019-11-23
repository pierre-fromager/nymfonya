<?php

namespace App\Component\Auth;

use Nymfonya\Component\Container;

interface AdapterInterface extends AuthInterface
{

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container);
}
