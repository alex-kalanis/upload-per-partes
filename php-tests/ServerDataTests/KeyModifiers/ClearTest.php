<?php

namespace ServerDataTests\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData;


class ClearTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new ServerData\KeyModifiers\Clear();
        $this->assertEquals('lkjhg', $lib->getKeyForStorage('lkjhg'));
        $this->assertEquals('lkjhg', $lib->pack('lkjhg'));
        $this->assertEquals('lkjhg', $lib->unpack('lkjhg'));
    }
}
