<?php

namespace GenerateKeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\GenerateKeys;


class Md5Test extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $data = new Data();
        $data->sharedKey = 'lkjhg';
        $lib = new GenerateKeys\Md5();
        $this->assertEquals('a75d6a841eafd550b0a27293ee054614', $lib->generateKey($data));
    }
}
