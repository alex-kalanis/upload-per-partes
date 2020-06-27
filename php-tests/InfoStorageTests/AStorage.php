<?php

namespace InfoStorageTests;

use CommonTestClass;
use UploadPerPartes\InfoStorage;
use UploadPerPartes\Uploader\Translations;

abstract class AStorage extends CommonTestClass
{
    public function tearDown(): void
    {
        if (is_file($this->mockTestFile())) {
            $this->mockStorage()->remove($this->mockTestFile());
        }
        parent::tearDown();
    }

    protected function mockStorage(): InfoStorage\AStorage
    {
        return new InfoStorage\Volume(Translations::init());
    }
}