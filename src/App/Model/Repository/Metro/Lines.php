<?php

declare(strict_types=1);

namespace App\Model\Repository\Metro;

use Nymfonya\Component\Container;
use App\Component\Model\Orm\IOrm;
use App\Component\Model\Orm\Orm;

class Lines extends Orm implements IOrm
{

    const _LIGNES = 'lignes';
    const _SRC = 'src';
    const _HSRC = 'hsrc';
    const _DST = 'dst';
    const _HDST = 'hdst';
    const _DIST = 'dist';

    /**
     * table name
     * @var string
     */
    protected $tablename = 'metro_lines';

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
