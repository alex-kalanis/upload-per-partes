<?php

namespace TargetTests\Local\DrivingFile\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers;
use kalanis\UploadPerPartes\UploadException;


class Base64Test extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new KeyModifiers\Base64();
        $this->assertEquals('test string', $lib->unpack($lib->pack('test string')));
    }

    /**
     * @throws UploadException
     */
    public function testFail(): void
    {
        $lib = new KeyModifiers\Base64();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot decode incoming data!');
        $lib->unpack('test string which is not a b64 data!!!');
    }
}
