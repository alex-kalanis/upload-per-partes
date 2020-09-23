<?php

namespace BasicTests;

use CommonTestClass;
use Support;
use UploadPerPartes\InfoFormat;
use UploadPerPartes\Uploader;

class DriveFileTest extends CommonTestClass
{
    public function tearDown(): void
    {
        $driveFile = $this->getDriveFile();
        if ($driveFile->exists($this->mockKey())) {
            $driveFile->remove($this->mockKey());
        }
        parent::tearDown();
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testThru(): void
    {
        $driveFile = $this->getDriveFile();
        $this->assertTrue($driveFile->write($this->mockKey(), $this->mockData()));
        $data = $driveFile->read($this->mockKey());
        $this->assertInstanceOf('\UploadPerPartes\InfoFormat\Data', $data);
        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempLocation);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
        $driveFile->remove($this->mockKey());
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     * @expectedException \UploadPerPartes\Exceptions\ContinuityUploadException
     * @expectedExceptionMessage DRIVEFILE ALREADY EXISTS
     */
    public function testWriteFail(): void
    {
        $driveFile = $this->getDriveFile();
        $data = $this->mockData();
        $this->assertTrue($driveFile->write($this->mockKey(), $data));
        $this->assertTrue($driveFile->exists($this->mockKey()));
        $driveFile->write($this->mockKey(), $data, true); // fail
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testUpdate(): void
    {
        $driveFile = $this->getDriveFile();
        $data = $this->mockData();
        $this->assertTrue($driveFile->write($this->mockKey(), $data));
        $this->assertTrue($driveFile->updateLastPart($this->mockKey(), $data, $data->lastKnownPart + 1));
        $driveFile->remove($this->mockKey());
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage DRIVEFILE IS NOT CONTINUOUS
     */
    public function testUpdateFail(): void
    {
        $driveFile = $this->getDriveFile();
        $data = $this->mockData();
        $this->assertTrue($driveFile->write($this->mockKey(), $data));
        $driveFile->updateLastPart($this->mockKey(), $data, $data->lastKnownPart + 5); // fail
    }

    protected function mockKey(): string
    {
        return 'fghjkl' . Uploader\TargetSearch::FILE_DRIVER_SUFF;
    }

    protected function getDriveFile(): Uploader\DriveFile
    {
        $lang = Uploader\Translations::init();
        $storage = new Support\InfoRam($lang);
        $target = new Uploader\TargetSearch($lang);
        $key = new Support\Key($lang, $target);
        $format = new InfoFormat\Json();
        return new Uploader\DriveFile($lang, $storage, $format, $key);
    }
}