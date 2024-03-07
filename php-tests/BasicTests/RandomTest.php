<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Uploader;


class RandomTest extends CommonTestClass
{
    public function testThru(): void
    {
        Uploader\RandomStrings::randomLength();
        $this->assertNotEmpty(Uploader\RandomStrings::generate());
    }

    public function testRandom(): void
    {
        $this->assertEquals('aaaaaaa', Uploader\RandomStrings::generateRandomText(7, ['a','a','a','a']));
    }
}
