<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\Uploader;
use Support;


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
     * @throws Exceptions\UploadException
     */
    public function testThru(): void
    {
        $driveFile = $this->getDriveFile();
        $this->assertTrue($driveFile->write($this->mockData()));
        $data = $driveFile->read($this->mockKey());
        $this->assertInstanceOf(InfoFormat\Data::class, $data);
        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempLocation);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
        $driveFile->remove($this->mockKey());
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testWriteFail(): void
    {
        $driveFile = $this->getDriveFile();
        $data = $this->mockData();
        $this->assertTrue($driveFile->write($data));
        $this->assertTrue($driveFile->exists($this->mockKey()));
        $this->expectException(Exceptions\ContinuityUploadException::class);
        $driveFile->write($data, true); // fail
        $this->expectExceptionMessageMatches('DRIVEFILE ALREADY EXISTS');
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testUpdate(): void
    {
        $driveFile = $this->getDriveFile();
        $data = $this->mockData();
        $this->assertTrue($driveFile->write($data));
        $this->assertTrue($driveFile->updateLastPart($data, $data->lastKnownPart + 1));
        $driveFile->remove($this->mockKey());
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testUpdateFail(): void
    {
        $driveFile = $this->getDriveFile();
        $data = $this->mockData();
        $this->assertTrue($driveFile->write($data));
        $this->expectException(Exceptions\UploadException::class);
        $driveFile->updateLastPart($data, $data->lastKnownPart + 5); // fail
        $this->expectExceptionMessageMatches('DRIVEFILE IS NOT CONTINUOUS');
    }

    protected function mockKey(): Data
    {
        $data = new Data();
        $data->sharedKey = 'fghjkl' . Uploader\TargetSearch::FILE_DRIVER_SUFF;
        return $data;
    }

    protected function getDriveFile(): Uploader\DriveFile
    {
        $lang = new Uploader\Translations();
        $storage = new Support\InfoRam($lang);
        $key = new Support\ServerKey();
        $format = new InfoFormat\Json();
        return new Uploader\DriveFile($storage, $format, $key, $lang);
    }
}
