<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Http\Request;
use App\Model\AbstractSearch;

/**
 * @covers \App\Model\AbstractSearch::<public>
 */
class AppModelSearchTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * request
     *
     * @var Request
     */
    protected $request;

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
        $this->request = new Request();
        $this->instance = new class ($this->request) extends AbstractSearch
        {

            protected $inst;

            public function __construct(Request $req)
            {
                parent::__construct($req);
                $this->inst = $this;
            }

            protected function setItem(array $item): AbstractSearch
            {
                return $this->inst;
            }
        };
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
}
