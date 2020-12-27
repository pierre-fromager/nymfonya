<?php

declare(strict_types=1);

namespace App\Component\Db;

use Nymfonya\Component\Container;

abstract class Migration extends Core
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
        parent::__construct($container);
    }

    /**
     * run migration for a given repository
     *
     * @return Migration
     */
    public function migrate(): Migration
    {
        if ($this->canMigrate()) {
            $this->runCreate()->runInsert()->runIndex();
        }
        return $this;
    }

    /**
     * check if migration can run
     *
     * @return boolean
     */
    abstract protected function canMigrate(): bool;

    /**
     * process create table
     *
     * @return string
     */
    abstract protected function runCreate(): Migration;

    /**
     * process insert into table
     *
     * @return string
     */
    abstract protected function runInsert(): Migration;

    /**
     * process alter index table
     *
     * @return string
     */
    abstract protected function runIndex(): Migration;
}
