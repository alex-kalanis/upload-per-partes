<?php

namespace StorageTests;

use CommonTestClass;
use UploadPerPartes\Storage;
use UploadPerPartes\Uploader\Translations;

abstract class AStorage extends CommonTestClass
{
    public function tearDown()
    {
        if (is_file($this->mockTestFile())) {
            $this->mockStorage()->remove($this->mockTestFile());
        }
        parent::tearDown();
    }

    protected function mockStorage(): Storage\AStorage
    {
        return new Storage\Volume(Translations::init());
    }
}