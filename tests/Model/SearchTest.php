<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Container;

use App\Model\AbstractSearch;

/**
 * @covers \App\Model\AbstractSearch::<public>
 */
class AppModelSearchTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';
    const ASSET_PATH = '/../../assets/tests/model/';
    const CSV_FILENAME = '/accounts.csv';

    /**
     * config
     *
     * @var Config
     */
    protected $config;

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * csv filename
     *
     * @var string
     */
    protected $filename;

    /**
     * instance
     *
     * @var Search
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->filename = realpath(
            __DIR__ . self::ASSET_PATH
        ) . self::CSV_FILENAME;
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->createCsv();
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->instance = new class ($this->container) extends AbstractSearch
        {
            protected $inst;
            public function __construct(Container $container)
            {
                parent::__construct($container);
                $this->inst = $this;
            }
            protected function setItem(array $item): AbstractSearch
            {
                $this->datas[] = $item;
                return $this->inst;
            }
        };
    }

    /**
     * create csv test file if not exists
     *
     * @return void
     */
    protected function createCsv()
    {
        if (!file_exists($this->filename)) {
            $accounts = $this->config->getSettings(Config::_ACCOUNTS);
            $fp = fopen($this->filename, 'w');
            foreach ($accounts as $record) {
                fputcsv($fp, array_values($record));
            }
            fclose($fp);
            unset($fp, $accounts, $config);
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->request = null;
    }

    /**
     * testInstance
     * @covers App\Model\AbstractSearch::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof AbstractSearch);
    }

    /**
     * testSetFilename
     * @covers App\Model\AbstractSearch::setFilename
     */
    public function testSetFilename()
    {
        $this->assertTrue(
            $this->instance->setFilename('') instanceof AbstractSearch
        );
    }

    /**
     * testSetFilter
     * @covers App\Model\AbstractSearch::setFilter
     */
    public function testSetFilter()
    {
        $this->assertTrue(
            $this->instance->setFilter('') instanceof AbstractSearch
        );
    }

    /**
     * testSetSeparator
     * @covers App\Model\AbstractSearch::setSeparator
     */
    public function testSetSeparator()
    {
        $this->assertTrue(
            $this->instance->setSeparator('') instanceof AbstractSearch
        );
    }

    /**
     * testGet
     * @covers App\Model\AbstractSearch::get
     */
    public function testGet()
    {
        $this->assertTrue(is_array($this->instance->get('')));
    }

    /**
     * testReadFromStreamException
     * @covers App\Model\AbstractSearch::readFromStream
     */
    public function testReadFromStreamException()
    {
        $this->expectException(\Exception::class);
        $this->assertTrue(
            $this->instance->readFromStream() instanceof AbstractSearch
        );
    }

    /**
     * testGetService
     * @covers App\Model\AbstractSearch::getService
     */
    public function testGetService()
    {
        $this->assertTrue(
            $this->instance->getService(
                \App\Config::class
            ) instanceof Config
        );
    }

    /**
     * testReadFromStream
     * @covers App\Model\AbstractSearch::readFromStream
     */
    public function testReadFromStream()
    {
        $sfn = $this->instance->setFilename($this->filename);
        $this->assertTrue($sfn instanceof AbstractSearch);
        $sft = $this->instance->setFilter('/^(.*),(.*),(.*),(.*),(.*)/');
        $this->assertTrue($sft instanceof AbstractSearch);
        $sse = $this->instance->setseparator(',');
        $this->assertTrue($sse instanceof AbstractSearch);
        $rff = $this->instance->readFromStream();
        $this->assertTrue($rff instanceof AbstractSearch);
        $datas = $this->instance->get();
        $this->assertTrue(is_array($datas));
        $this->assertNotEmpty($datas);
        $this->assertTrue(count($datas[0]) === 6);
    }
}
