<?php

namespace KeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Keys;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use kalanis\UploadPerPartes\Uploader\Translations;
use Support;


class RedisTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lang = new Translations();
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $target->setRemoteFileName('poiuztrewq')->setTargetDir($this->getTestDir())->process();
        $lib = new Keys\Redis($target, $lang);
        $lib->generateKeys();

        $this->assertEquals(md5('poiuztrewq'), $lib->getSharedKey());
        $this->assertEquals(Keys\Redis::PREFIX . 'lkjhg', $lib->fromSharedKey('lkjhg'));
    }
}
