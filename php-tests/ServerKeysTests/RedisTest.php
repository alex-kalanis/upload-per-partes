<?php

namespace ServerKeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\ServerKeys;


class RedisTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $data = new Data();
        $data->sharedKey = 'lkjhg';
        $lib = new ServerKeys\Redis();
        $this->assertEquals(ServerKeys\Redis::PREFIX . 'lkjhg', $lib->fromData($data));
    }
}
