<?php

namespace ServerDataTests\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData;


class Md5Test extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new ServerData\KeyModifiers\Md5();
        $this->assertEquals('a75d6a841eafd550b0a27293ee054614', $lib->getKeyForStorage('lkjhg'));
        $this->assertEquals('lkjhg', $lib->pack('lkjhg'));
        $this->assertEquals('lkjhg', $lib->unpack('lkjhg'));
    }
}
