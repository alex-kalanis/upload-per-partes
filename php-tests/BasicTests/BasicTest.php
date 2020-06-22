<?php

namespace BasicTests;

use CommonTestClass;
use UploadPerPartes\Uploader\Calculates;

class BasicTest extends CommonTestClass
{
    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testCalculate()
    {
        $lib = Calculates::init();
        $this->assertEquals(Calculates::DEFAULT_BYTES_PER_PART, $lib->getBytesPerPart());

        $lib2 = Calculates::init(20);
        $this->assertEquals(20, $lib2->getBytesPerPart());
        $this->assertEquals(2, $lib2->calcParts(35));
        $this->assertEquals(2, $lib2->calcParts(40));
        $this->assertEquals(3, $lib2->calcParts(41));
    }
}