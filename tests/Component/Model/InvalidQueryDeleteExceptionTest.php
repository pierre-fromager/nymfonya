<?php

namespace Tests\Component\Model;

use PHPUnit\Framework\TestCase as PFT;
use App\Component\Model\Orm\InvalidQueryDeleteException;

/**
 * @covers \App\Component\Model\Orm\InvalidQueryDeleteException::<public>
 */
class InvalidQueryDeleteExceptionTest extends PFT
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
     * @covers App\Component\Model\Orm\InvalidQueryDeleteException::__construct
     */
    public function testInstance()
    {
        $iqde = new InvalidQueryDeleteException(
            InvalidQueryDeleteException::MSG_CONDITION
        );
        $this->assertTrue($iqde instanceof InvalidQueryDeleteException);
        $this->assertEquals(
            $iqde->getMessage(),
            InvalidQueryDeleteException::MSG_CONDITION
        );
        $this->assertEquals(
            (string) $iqde,
            InvalidQueryDeleteException::MSG_CONDITION
        );
    }
}
