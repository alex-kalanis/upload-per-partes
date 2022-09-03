<?php

namespace InfoFormatTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoStorage;
use kalanis\UploadPerPartes\Uploader\Translations;


abstract class AFormats extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function tearDown(): void
    {
        if (is_file($this->mockTestFile())) {
            $lib = new InfoStorage\Volume(new Translations());
            $lib->remove($this->mockTestFile());
        }
        parent::tearDown();
    }
}
