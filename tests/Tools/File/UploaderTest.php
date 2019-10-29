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
     * return an expected fake global $_FILES
     *
     * @return array
     */
    protected function getFakeFile(): array
    {
        return [
            'file' => [
                'name' => 'test.csv',
                'tmp_name' => 'test.csv',
                'error' => 0,
                'type' => 'text/csv',
                'size' => 1000,
            ]
        ];
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
     * constantsProvider
     * @return Array
     */
    public function constantsProvider()
    {
        return [
            ['FIELD'],
            ['UPLOAD_ERR_INI_SIZE'],
            ['UPLOAD_ERR_FORM_SIZE'],
            ['UPLOAD_ERR_NO_TMP_DIR'],
            ['UPLOAD_ERR_CANT_WRITE'],
            ['UPLOAD_ERR_EXTENSION'],
            ['UPLOAD_ERR_PARTIAL'],
            ['UPLOAD_ERR_NO_FILE'],
            ['UPLOAD_ERR_UNKOWN'],
        ];
    }

    /**
     * testConstants
     * @covers App\Tools\File\Uploader::__construct
     * @dataProvider constantsProvider
     */
    public function testConstants($k)
    {
        $class = new \ReflectionClass(Uploader::class);
        $this->assertArrayHasKey($k, $class->getConstants());
        unset($class);
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
     * @covers App\Tools\File\Uploader::setFile
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
        $sfe = self::getMethod('setFile')->invokeArgs(
            $this->instance,
            [$this->getFakeFile()]
        );
        $this->assertTrue($sfe instanceof Uploader);
        $this->assertTrue(
            $this->instance->process() instanceof Uploader
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
        $sfe = self::getMethod('setFile')->invokeArgs(
            $this->instance,
            [$this->getFakeFile()]
        );
        $this->assertTrue($sfe instanceof Uploader);
        $sfi = self::getMethod('setFileInfos')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($sfi instanceof Uploader);
    }

    /**
     * testSetFile
     * @covers App\Tools\File\Uploader::setFile
     */
    public function testSetFile()
    {
        $sfe = self::getMethod('setFile')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($sfe instanceof Uploader);
    }

    /**
     * errorProvider
     * @return Array
     */
    public function errorProvider()
    {
        return [
            [UPLOAD_ERR_INI_SIZE, Uploader::UPLOAD_ERR_INI_SIZE],
            [UPLOAD_ERR_FORM_SIZE, Uploader::UPLOAD_ERR_FORM_SIZE],
            [UPLOAD_ERR_NO_TMP_DIR, Uploader::UPLOAD_ERR_NO_TMP_DIR],
            [UPLOAD_ERR_CANT_WRITE, Uploader::UPLOAD_ERR_CANT_WRITE],
            [UPLOAD_ERR_EXTENSION, Uploader::UPLOAD_ERR_EXTENSION],
            [UPLOAD_ERR_PARTIAL, Uploader::UPLOAD_ERR_PARTIAL],
            [UPLOAD_ERR_NO_FILE, Uploader::UPLOAD_ERR_NO_FILE],
            [2000, Uploader::UPLOAD_ERR_UNKOWN],
        ];
    }

    /**
     * testSetErrorCode
     * @covers App\Tools\File\Uploader::setErrorCode
     * @covers App\Tools\File\Uploader::getInfos
     * @covers App\Tools\File\Uploader::setErrorMessage
     * @dataProvider errorProvider
     */
    public function testSetErrorCode($code, $expMsg)
    {
        self::getMethod('setErrorCode')->invokeArgs($this->instance, [$code]);
        self::getMethod('setErrorMessage')->invokeArgs($this->instance, []);
        $this->assertEquals($this->instance->getInfos()['errorMsg'], $expMsg);
    }

    /**
     * testSetErrorCodeNoError
     * @covers App\Tools\File\Uploader::setFile
     * @covers App\Tools\File\Uploader::setErrorCode
     * @covers App\Tools\File\Uploader::getInfos
     * @covers App\Tools\File\Uploader::setErrorMessage
     */
    public function testSetErrorCodeNoError()
    {
        self::getMethod('setFile')->invokeArgs(
            $this->instance,
            [$this->getFakeFile()]
        );
        self::getMethod('setErrorCode')->invokeArgs($this->instance, [0]);
        self::getMethod('setErrorMessage')->invokeArgs($this->instance, []);
        $this->assertEquals($this->instance->getInfos()['errorMsg'], '');
    }
}
