<?php

namespace App\Component\Auth\Adapters;

use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;

class Repository implements AdapterInterface
{

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * auth process
     *
     * @return array
     */
    public function auth(string $login, string $password): array
    {
        return [];
    }
}
