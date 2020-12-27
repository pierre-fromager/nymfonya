<?php

namespace Tests\Component\Model;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Model\Orm\InvalidQueryUpdateException;

/**
 * @covers \App\Component\Model\Orm\InvalidQueryUpdateException::<public>
 */
class InvalidQueryUpdateExceptionTest extends PFT
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
        $iqde = new InvalidQueryUpdateException(
            InvalidQueryUpdateException::MSG_CONDITION
        );
        $this->assertTrue($iqde instanceof InvalidQueryUpdateException);
        $this->assertEquals(
            $iqde->getMessage(),
            InvalidQueryUpdateException::MSG_CONDITION
        );
        $this->assertEquals(
            (string) $iqde,
            InvalidQueryUpdateException::MSG_CONDITION
        );
    }
}
