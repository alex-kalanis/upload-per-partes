<?php

namespace DataStorageTests;


use CommonTestClass;
use kalanis\UploadPerPartes\DataStorage;
use kalanis\UploadPerPartes\Uploader\Translations;


abstract class AStorage extends CommonTestClass
{
    public function setUp(): void
    {
        if (is_file($this->mockTestFile())) {
            chmod($this->mockTestFile(), 0555);
            unlink($this->mockTestFile());
        }
        if (is_dir($this->mockTestFile())) {
            rmdir($this->mockTestFile());
        }
        parent::setUp();
    }

    public function tearDown(): void
    {
        if (is_file($this->mockTestFile())) {
            chmod($this->mockTestFile(), 0555);
            unlink($this->mockTestFile());
        }
        if (is_dir($this->mockTestFile())) {
            rmdir($this->mockTestFile());
        }
        parent::tearDown();
    }

    protected function mockStorage(): DataStorage\AStorage
    {
        return new DataStorage\VolumeBasic(new Translations());
    }
}
