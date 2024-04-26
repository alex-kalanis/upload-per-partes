<?php

namespace TargetTests\Local\Checksums;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\Checksums;
use kalanis\UploadPerPartes\UploadException;


class Sha1Test extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new Checksums\Sha1();
        $this->assertEquals('sha1', $lib->getMethod());
        $this->assertEquals('661295c9cbf9d6b2f6428414504a8deed3020641', $lib->checksum('test string'));
    }
}
