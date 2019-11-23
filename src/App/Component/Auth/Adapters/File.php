<?php

namespace App\Component\Auth\Adapters;

use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;
use App\Model\Accounts;

class File implements AdapterInterface
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
        return (new Accounts($this->container))->auth(
            $login,
            $password
        );
    }
}
