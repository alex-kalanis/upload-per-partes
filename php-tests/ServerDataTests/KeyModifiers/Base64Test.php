<?php

namespace ServerDataTests\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData;


class Base64Test extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new ServerData\KeyModifiers\Base64();
        $this->assertEquals('bGtqaGc=', $lib->getKeyForStorage('lkjhg'));
        $this->assertEquals('bGtqaGc=', $lib->pack('lkjhg'));
        $this->assertEquals('lkjhg', $lib->unpack('bGtqaGc='));
    }

    /**
     * @throws UploadException
     */
    public function testUnpackFail(): void
    {
        $lib = new ServerData\KeyModifiers\Base64();
        $this->expectExceptionMessage('CANNOT DECODE INCOMING DATA');
        $this->expectException(UploadException::class);
        $lib->unpack('--not-encoded-text--');
    }
}
