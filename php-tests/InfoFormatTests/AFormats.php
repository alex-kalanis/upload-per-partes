<?php

namespace InfoFormatTests;

use CommonTestClass;
use UploadPerPartes\InfoStorage;
use UploadPerPartes\Uploader\Translations;

abstract class AFormats extends CommonTestClass
{
    public function tearDown()
    {
        if (is_file($this->mockTestFile())) {
            $lib = new InfoStorage\Volume(Translations::init());
            $lib->remove($this->mockTestFile());
        }
        parent::tearDown();
    }
}