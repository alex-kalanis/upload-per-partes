<?php

namespace KeysTests;

use CommonTestClass;
use UploadPerPartes\Keys;
use UploadPerPartes\Storage\TargetSearch;
use UploadPerPartes\Uploader\Translations;

class KeysTest extends CommonTestClass
{
    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testInit()
    {
        $lang = Translations::init();
        $target = new TargetSearch($lang);
        $this->assertInstanceOf('\UploadPerPartes\Keys\SimpleVolume', Keys\AKey::getVariant($lang, $target, Keys\AKey::VARIANT_VOLUME));
        $this->assertInstanceOf('\UploadPerPartes\Keys\Random', Keys\AKey::getVariant($lang, $target, Keys\AKey::VARIANT_RANDOM));
        $this->assertInstanceOf('\UploadPerPartes\Keys\Redis', Keys\AKey::getVariant($lang, $target, Keys\AKey::VARIANT_REDIS));
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage KEY VARIANT NOT SET
     */
    public function testInitFail()
    {
        $lang = Translations::init();
        $target = new TargetSearch($lang);
        Keys\AKey::getVariant($lang, $target, 0);
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage SHARED KEY IS EMPTY
     */
    public function testSharedFail()
    {
        $lang = Translations::init();
        $lib = new Keys\Random($lang, new TargetSearch($lang));
        $lib->getSharedKey(); // no key set!
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testRandom()
    {
        $this->assertEquals('aaaaaaa', Keys\Random::generateRandomText(7, ['a','a','a','a']));

        $lang = Translations::init();
        $lib = new Keys\Random($lang, new TargetSearch($lang));
        $this->assertEquals('abcdefghi' . TargetSearch::FILE_DRIVER_SUFF, $lib->fromSharedKey('abcdefghi'));
    }
}