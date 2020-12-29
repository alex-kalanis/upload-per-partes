<?php

namespace KeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Keys;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use kalanis\UploadPerPartes\Uploader\Translations;


class RedisTest extends CommonTestClass
{
    /**
     * @throws \kalanis\UploadPerPartes\Exceptions\UploadException
     */
    public function testThru(): void
    {
        $lang = Translations::init();
        $target = new TargetSearch($lang);
        $target->setRemoteFileName('poiuztrewq')->setTargetDir($this->getTestDir())->process();
        $lib = new Keys\Redis($lang, $target);
        $lib->generateKeys();

        $this->assertEquals(md5('poiuztrewq'), $lib->getSharedKey());
        $this->assertEquals(Keys\Redis::PREFIX . 'lkjhg', $lib->fromSharedKey('lkjhg'));
    }
}