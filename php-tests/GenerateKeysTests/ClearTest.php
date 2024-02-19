<?php

namespace GenerateKeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\GenerateKeys;


class ClearTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $data = new Data();
        $data->sharedKey = 'lkjhg';
        $lib = new GenerateKeys\Clear();
        $this->assertEquals('lkjhg', $lib->generateKey($data));
    }
}
