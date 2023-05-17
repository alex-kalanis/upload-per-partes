<?php

namespace KeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Keys;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use kalanis\UploadPerPartes\Uploader\Translations;
use Support;


class VolumeTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lang = new Translations();
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $target->setRemoteFileName('poiuztrewq')->setTargetDir('/tmp/')->process();
        $lib = new Keys\SimpleVolume($target, $lang);
        $lib->generateKeys();

        $this->assertEquals(base64_encode('/tmp/poiuztrewq' . TargetSearch::FILE_DRIVER_SUFF), $lib->getSharedKey());
        $this->assertEquals('/tmp/lkjhg', $lib->fromSharedKey(base64_encode('/tmp/lkjhg')));
        $this->expectException(UploadException::class);
        $lib->fromSharedKey('**/tmp/lkjhg'); // aaand failed... - chars outside the b64
        $this->expectExceptionMessageMatches('SHARED KEY IS INVALID');
    }
}
