<?php

namespace TargetTests\Local\ContentDecoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\ContentDecoders;
use kalanis\UploadPerPartes\UploadException;


class HexTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new ContentDecoders\Hex();
        $this->assertEquals('hex', $lib->getMethod());
        $this->assertEquals('test string', $lib->decode(bin2hex('test string')));
    }

    /**
     * @throws UploadException
     */
    public function testFail(): void
    {
        $lib = new ContentDecoders\Hex();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot decode incoming data!');
        $lib->decode('test string which is not a hex data');
    }
}
