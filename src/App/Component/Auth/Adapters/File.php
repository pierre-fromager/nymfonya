<?php

declare(strict_types=1);

namespace App\Component\Auth\Adapters;

use Nymfonya\Component\Container;
use App\Component\Auth\AdapterInterface;
use App\Model\Accounts;

/**
 * Adapter File let auth from csv file accounts
 * Decryption required on password.
 */
class File implements AdapterInterface
{

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * model accounts
     *
     * @var Accounts
     */
    protected $modelAccounts;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->modelAccounts = new Accounts($this->container);
    }

    /**
     * auth process
     *
     * @return array
     */
    public function auth(string $login, string $password): array
    {
        return $this->modelAccounts->auth($login, $password);
    }

    /**
     * return account by id
     *
     * @return array
     */
    public function getById(int $id): array
    {
        return $this->modelAccounts->getById($id);
    }
}
