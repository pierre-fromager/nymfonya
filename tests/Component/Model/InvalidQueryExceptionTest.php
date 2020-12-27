<?php

namespace Tests\Component\Model;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Model\Orm\InvalidQueryException;

/**
 * @covers \App\Component\Model\Orm\InvalidQueryException::<public>
 */
class InvalidQueryExceptionTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    /**
     * testInstance
     * @covers App\Component\Model\Orm\InvalidQueryUpdateException::__construct
     */
    public function testInstance()
    {
        $iqde = new InvalidQueryException(
            InvalidQueryException::MSG_INSTANCE
        );
        $this->assertTrue($iqde instanceof InvalidQueryException);
        $this->assertEquals(
            $iqde->getMessage(),
            InvalidQueryException::MSG_INSTANCE
        );
        $this->assertEquals(
            (string) $iqde,
            InvalidQueryException::MSG_INSTANCE
        );
    }
}
