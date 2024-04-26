<?php

namespace TargetTests\Local\ContentDecoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\ContentDecoders;
use kalanis\UploadPerPartes\UploadException;


class RawTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new ContentDecoders\Raw();
        $this->assertEquals('raw', $lib->getMethod());
        $this->assertEquals('test string', $lib->decode('test string'));
    }
}
