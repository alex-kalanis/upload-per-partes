<?php

namespace KeysTests;

use CommonTestClass;
use UploadPerPartes\DataStorage\TargetSearch;
use UploadPerPartes\Keys;
use UploadPerPartes\Uploader\Translations;

class VolumeTest extends CommonTestClass
{
    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testThru(): void
    {
        $lang = Translations::init();
        $target = new TargetSearch($lang);
        $target->setRemoteFileName('poiuztrewq')->setTargetDir('/tmp/')->process();
        $lib = new Keys\SimpleVolume($lang, $target);
        $lib->generateKeys();

        $this->assertEquals(base64_encode('/tmp/poiuztrewq' . TargetSearch::FILE_DRIVER_SUFF), $lib->getSharedKey());
        $this->assertEquals('/tmp/lkjhg', $lib->fromSharedKey(base64_encode('/tmp/lkjhg')));
    }
}