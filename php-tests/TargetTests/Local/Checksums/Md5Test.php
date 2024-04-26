<?php

namespace TargetTests\Local\Checksums;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\Checksums;
use kalanis\UploadPerPartes\UploadException;


class Md5Test extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new Checksums\Md5();
        $this->assertEquals('md5', $lib->getMethod());
        $this->assertEquals('6f8db599de986fab7a21625b7916589c', $lib->checksum('test string'));
    }
}
