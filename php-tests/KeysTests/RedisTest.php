<?php

namespace KeysTests;

use CommonTestClass;
use UploadPerPartes\DataStorage\TargetSearch;
use UploadPerPartes\Keys;
use UploadPerPartes\Uploader\Translations;

class RedisTest extends CommonTestClass
{
    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testThru()
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