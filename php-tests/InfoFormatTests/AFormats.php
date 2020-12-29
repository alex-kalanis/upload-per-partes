<?php

namespace InfoFormatTests;


use CommonTestClass;
use kalanis\UploadPerPartes\InfoStorage;
use kalanis\UploadPerPartes\Uploader\Translations;


abstract class AFormats extends CommonTestClass
{
    public function tearDown(): void
    {
        if (is_file($this->mockTestFile())) {
            $lib = new InfoStorage\Volume(Translations::init());
            $lib->remove($this->mockTestFile());
        }
        parent::tearDown();
    }
}