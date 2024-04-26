<?php

namespace TargetTests\Local\ContentDecoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\ContentDecoders;
use kalanis\UploadPerPartes\UploadException;


class Base64Test extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new ContentDecoders\Base64();
        $this->assertEquals('base64', $lib->getMethod());
        $this->assertEquals('test string', $lib->decode(base64_encode('test string')));
    }

    /**
     * @throws UploadException
     */
    public function testFail(): void
    {
        $lib = new ContentDecoders\Base64();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot decode incoming data!');
        $lib->decode('test string which is not a b64 data!!!');
    }
}
