<?php

namespace FormatTests;

use CommonTestClass;
use UploadPerPartes\DataFormat;
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

    protected function mockData(): DataFormat\Data
    {
        return DataFormat\Data::init()->setData(
            'abcdef',
            $this->getTestDir() . 'abcdef',
            123456,
            12,
            64,
            7
        );
    }
}