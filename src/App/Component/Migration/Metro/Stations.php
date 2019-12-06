<?php

namespace App\Component\Migration\Metro;

use App\Component\Model\Orm\Orm;
use App\Component\Db\Migration;
use App\Model\Repository\Metro\Stations as StationsRepository;
use Nymfonya\Component\Container;
use SplFileObject;

class Stations extends Migration
{
    const MIG_FIELDS = [
        'id', 'lon', 'lat', 'name', 'h'
    ];
    const CSV_FIXTURE = '/../../../../../assets/model/metro/stations.csv';
    const MEM_LIM = 'memory_limit';

    /**
     * repository
     *
     * @var Orm
     */
    protected $repository;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->repository = new StationsRepository($container);
        $this->fromOrm($this->repository);
    }

    /**
     * check if migration can run
     *
     * @return boolean
     */
    protected function canMigrate(): bool
    {
        return (!$this->tableExists());
    }

    /**
     * create table
     *
     * @return Migration
     */
    protected function runCreate(): Migration
    {
        if (!$this->tableExists()) {
            $sql = sprintf(
                'CREATE TABLE `%s` (
                    `id` bigint(20) NOT NULL,
                    `lon` double NOT NULL,
                    `lat` double NOT NULL,
                    `name` varchar(150) NOT NULL,
                    `h` varchar(16) NOT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8',
                $this->repository->getTable()
            );
            $this->run($sql);
        }
        return $this;
    }

    /**
     * insert into table
     *
     * @return Migration
     */
    protected function runInsert(): Migration
    {
        $ml = ini_get(self::MEM_LIM);
        ini_set(self::MEM_LIM, '8M');
        if ($this->tableExists()) {
            $stream = new SplFileObject(
                __DIR__ . self::CSV_FIXTURE
            );
            $stream->setFlags(
                SplFileObject::READ_CSV |
                    SplFileObject::SKIP_EMPTY |
                    SplFileObject::READ_AHEAD |
                    SplFileObject::DROP_NEW_LINE
            );
            while (false === $stream->eof()) {
                $csvData = $stream->fgetcsv();
                if (!empty($csvData)) {
                    $data = array_combine(self::MIG_FIELDS, $csvData);
                    $this->repository->resetBuilder();
                    $this->repository->insert($data);
                    $this->run(
                        $this->repository->getSql(),
                        $this->repository->getBuilderValues()
                    );
                }
                unset($csvData);
            }
            $stream = null;
            unset($stream, $lines);
        }
        ini_set(self::MEM_LIM, $ml);
        return $this;
    }

    /**
     * index table
     *
     * @return Migration
     */
    protected function runIndex(): Migration
    {
        $pkey = $this->repository->getPrimary();
        $sqlIndex = sprintf(
            'ALTER TABLE `%s`'
                . 'ADD PRIMARY KEY (`%s`),'
                . 'ADD KEY `%s` (`%s`),'
                . 'ADD KEY `name` (`name`),'
                . 'ADD KEY `h` (`h`)',
            $this->repository->getTable(),
            $pkey,
            $pkey,
            $pkey
        );
        $this->run($sqlIndex);
        $sqlAutoinc = sprintf('ALTER TABLE `%s`'
            . 'MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, '
            . 'AUTO_INCREMENT=1', $this->repository->getTable());
        $this->run($sqlAutoinc);
        return $this;
    }

    /**
     * return true if table exists
     *
     * @return boolean
     */
    protected function tableExists(): bool
    {
        $sqlWhere = " WHERE table_schema = '%s' AND table_name = '%s';";
        $sql = sprintf(
            "SELECT count(*) as counter FROM %s " . $sqlWhere,
            'information_schema.tables',
            $this->repository->getDatabase(),
            $this->repository->getTable()
        );
        $this->run($sql)->hydrate();
        $result = $this->getRowset()[0];
        $counter = (int) $result['counter'];
        return ($counter > 0);
    }

    /**
     * drop table if exists
     *
     * @return boolean
     */
    protected function dropTable(): bool
    {
        if (!$this->tableExists()) {
            return false;
        }
        $sql = 'DROP TABLE ' . $this->repository->getTable() . ';';
        $this->run($sql);
        return true;
    }
}
