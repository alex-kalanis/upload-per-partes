<?php

namespace TargetTests\Local\DrivingFile\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers;
use kalanis\UploadPerPartes\UploadException;


class SuffixTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new KeyModifiers\Suffix();
        $this->assertEquals('test string', $lib->unpack($lib->pack('test string')));
    }
}
