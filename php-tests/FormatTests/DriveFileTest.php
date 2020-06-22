<?php

namespace FormatTests;

use UploadPerPartes\DataFormat\AFormat;
use UploadPerPartes\Exceptions;
use UploadPerPartes\Storage\TargetSearch;
use UploadPerPartes\Storage\Volume;
use UploadPerPartes\Uploader\DriveFile;
use UploadPerPartes\Uploader\Translations;

class DriveFileTest extends AFormats
{
    /**
     * @throws Exceptions\UploadException
     */
    public function testSimple()
    {
        $this->assertTrue($this->mockDriveFile()->write($this->mockTestFile(), $this->mockData()));
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\ContinuityUploadException
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testDoubleNew()
    {
        $lib = $this->mockDriveFile();
        $this->assertTrue($lib->write($this->mockTestFile(), $this->mockData(), true));
        $lib->write($this->mockTestFile(), $this->mockData(), true);
    }

    /**
     * @throws Exceptions\ContinuityUploadException
     * @throws Exceptions\UploadException
     */
    public function testRead()
    {
        $lib = $this->mockDriveFile();
        $this->assertTrue($lib->write($this->mockTestFile(), $this->mockData()));
        $data = $lib->read($this->mockTestFile());

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempLocation);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
    }

    /**
     * @throws \UploadPerPartes\Exceptions\ContinuityUploadException
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     */
    public function testDoubleRemoval()
    {
        $lib = $this->mockDriveFile();
        $this->assertTrue($lib->write($this->mockTestFile(), $this->mockData()));
        $lib->remove($this->mockTestFile());
        $lib->remove($this->mockTestFile()); // dies here
    }

    /**
     * @param int $format
     * @return DriveFile
     * @throws Exceptions\UploadException
     */
    protected function mockDriveFile(int $format = AFormat::FORMAT_TEXT): DriveFile
    {
        $lang = Translations::init();
        $target = new TargetSearch($lang);
        return new DriveFile($lang, new Volume($lang), AFormat::getFormat($lang, $format), new Key($lang, $target));
    }
}