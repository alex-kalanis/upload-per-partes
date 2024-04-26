<?php

namespace TargetTests\Local\DrivingFile\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers;
use kalanis\UploadPerPartes\UploadException;


class ClearTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new KeyModifiers\Clear();
        $this->assertEquals('test string', $lib->unpack($lib->pack('test string')));
    }
}
