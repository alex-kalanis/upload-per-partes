<?php

namespace TargetTests\Local\DrivingFile\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers;
use kalanis\UploadPerPartes\UploadException;


class HexTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new DataModifiers\Hex();
        $this->assertEquals('test string', $lib->unpack($lib->pack('test string')));
    }

    /**
     * @throws UploadException
     */
    public function testFail(): void
    {
        $lib = new DataModifiers\Hex();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot decode incoming data!');
        $lib->unpack('test string which is not a hex data');
    }
}
