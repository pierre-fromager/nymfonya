<?php

namespace App\Model\Repository\Metro;

use Nymfonya\Component\Container;
use App\Component\Model\Orm\IOrm;
use App\Component\Model\Orm\Orm;

class Stations extends Orm implements IOrm
{

    /**
     * table name
     * @var string
     */
    protected $tablename = 'metro_stations';

    /**
     * table primary key
     * @var string
     */
    protected $primary = 'id';

    /**
     * database name
     * @var string
     */
    protected $database = 'nymfonya';

    /**
     * pool name
     * @var string
     */
    protected $slot = 'test';

    /**
     * instanciate
     *
     * @param Container $container
     * @return self
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }
}
