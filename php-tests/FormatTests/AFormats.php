<?php

namespace FormatTests;

use CommonTestClass;
use UploadPerPartes\Storage;
use UploadPerPartes\Uploader\Translations;

abstract class AFormats extends CommonTestClass
{
    public function tearDown()
    {
        if (is_file($this->mockTestFile())) {
            $lib = new Storage\Volume(Translations::init());
            $lib->remove($this->mockTestFile());
        }
        parent::tearDown();
    }
}