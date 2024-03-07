<?php

namespace ServerDataTests\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData;


class HexTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new ServerData\KeyModifiers\Hex();
        $this->assertEquals('6c6b6a6867', $lib->getKeyForStorage('lkjhg'));
        $this->assertEquals('6c6b6a6867', $lib->pack('lkjhg'));
        $this->assertEquals('lkjhg', $lib->unpack('6c6b6a6867'));
    }

    /**
     * @throws UploadException
     */
    public function testUnpackFail(): void
    {
        $lib = new ServerData\KeyModifiers\Hex();
        $this->expectExceptionMessage('CANNOT DECODE INCOMING DATA');
        $this->expectException(UploadException::class);
        $lib->unpack('--not-encoded-text--');
    }
}
