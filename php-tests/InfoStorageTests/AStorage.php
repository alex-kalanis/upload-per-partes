<?php

namespace InfoStorageTests;


use CommonTestClass;
use kalanis\UploadPerPartes\InfoStorage;
use kalanis\UploadPerPartes\Uploader\Translations;


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
