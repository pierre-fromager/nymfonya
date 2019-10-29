<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Tools\File\Uploader;

/**
 * @covers \App\Tools\File\Uploader::<public>
 */
class ToolsFileUploaderTest extends PFT
{

    const TEST_ENABLE = true;

    /**
     * instance
     *
     * @var Uploader
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
        $this->instance = new Uploader();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Uploader::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Tools\File\Uploader::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Uploader);
    }

    /**
     * testGetError
     * @covers App\Tools\File\Uploader::getError
     */
    public function testGetError()
    {
        $this->assertTrue(is_bool($this->instance->getError()));
        $this->assertTrue($this->instance->getError());
    }

    /**
     * testGetInfos
     * @covers App\Tools\File\Uploader::getInfos
     */
    public function testGetInfos()
    {
        $this->assertTrue(is_array($this->instance->getInfos()));
    }

    /**
     * testProcess
     * @covers App\Tools\File\Uploader::process
     */
    public function testProcess()
    {
        $this->assertTrue(
            $this->instance->process() instanceof Uploader
        );
        $this->assertEquals(
            $this->instance->getError(),
            UPLOAD_ERR_NO_FILE
        );
    }

    /**
     * testSetTargetPath
     * @covers App\Tools\File\Uploader::setTargetPath
     */
    public function testSetTargetPath()
    {
        $this->assertTrue(
            $this->instance->setTargetPath('') instanceof Uploader
        );
    }

    /**
     * testSetErrorMessage
     * @covers App\Tools\File\Uploader::setErrorMessage
     */
    public function testSetErrorMessage()
    {
        $sem = self::getMethod('setErrorMessage')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($sem instanceof Uploader);
    }

    /**
     * testSetFileInfos
     * @covers App\Tools\File\Uploader::setFileInfos
     */
    public function testSetFileInfos()
    {
        $sfi = self::getMethod('setFileInfos')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($sfi instanceof Uploader);
    }
}
