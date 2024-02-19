<?php

namespace GenerateKeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\GenerateKeys;


class Base64Test extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $data = new Data();
        $data->sharedKey = 'lkjhg';
        $lib = new GenerateKeys\Base64();
        $this->assertEquals('bGtqaGc=', $lib->generateKey($data));
    }
}
