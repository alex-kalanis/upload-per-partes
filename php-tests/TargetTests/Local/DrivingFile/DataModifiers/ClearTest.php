<?php

namespace TargetTests\Local\DrivingFile\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers;
use kalanis\UploadPerPartes\UploadException;


class ClearTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new DataModifiers\Clear();
        $this->assertEquals('test string', $lib->unpack($lib->pack('test string')));
    }
}
