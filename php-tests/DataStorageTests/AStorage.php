<?php

namespace DataStorageTests;

use CommonTestClass;
use UploadPerPartes\DataStorage;
use UploadPerPartes\Uploader\Translations;

abstract class AStorage extends CommonTestClass
{
    public function tearDown()
    {
        if (is_file($this->mockTestFile())) {
            unlink($this->mockTestFile());
        }
        if (is_dir($this->mockTestFile())) {
            rmdir($this->mockTestFile());
        }
        parent::tearDown();
    }

    protected function mockStorage(): DataStorage\AStorage
    {
        return new DataStorage\Volume(Translations::init());
    }
}