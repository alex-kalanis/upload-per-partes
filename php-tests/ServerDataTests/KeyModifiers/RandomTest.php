<?php

namespace ServerDataTests\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData;


class RandomTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new ServerData\KeyModifiers\Random();
        $this->assertNotEmpty($lib->getKeyForStorage('lkjhg'));
        $this->assertEquals('lkjhg', $lib->pack('lkjhg'));
        $this->assertEquals('lkjhg', $lib->unpack('lkjhg'));
    }
}
