<?php

namespace ServerKeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\ServerKeys;


class VolumeTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $data = new Data();
        $data->pathPrefix = '/tmp/';
        $data->sharedKey = 'lkjhg';
        $lib = new ServerKeys\Volume();
        $this->assertEquals('/tmp/lkjhg', $lib->fromData($data));
    }
}
