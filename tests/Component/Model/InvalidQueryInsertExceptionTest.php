<?php

namespace Tests\Component\Model;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Model\Orm\InvalidQueryInsertException;

/**
 * @covers \App\Component\Model\Orm\InvalidQueryInsertException::<public>
 */
class InvalidQueryInsertExceptionTest extends PFT
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
     * @covers App\Component\Model\Orm\InvalidQueryInsertException::__construct
     */
    public function testInstance()
    {
        $iqde = new InvalidQueryInsertException(
            InvalidQueryInsertException::MSG_PAYLOAD
        );
        $this->assertTrue($iqde instanceof InvalidQueryInsertException);
        $this->assertEquals(
            $iqde->getMessage(),
            InvalidQueryInsertException::MSG_PAYLOAD
        );
        $this->assertEquals(
            (string) $iqde,
            InvalidQueryInsertException::MSG_PAYLOAD
        );
    }
}
