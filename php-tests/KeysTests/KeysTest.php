<?php

namespace KeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Keys;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use kalanis\UploadPerPartes\Uploader\Translations;


class KeysTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $lang = Translations::init();
        $target = new TargetSearch($lang);
        $this->assertInstanceOf('\kalanis\UploadPerPartes\Keys\SimpleVolume', Keys\AKey::getVariant($lang, $target, Keys\AKey::VARIANT_VOLUME));
        $this->assertInstanceOf('\kalanis\UploadPerPartes\Keys\Random', Keys\AKey::getVariant($lang, $target, Keys\AKey::VARIANT_RANDOM));
        $this->assertInstanceOf('\kalanis\UploadPerPartes\Keys\Redis', Keys\AKey::getVariant($lang, $target, Keys\AKey::VARIANT_REDIS));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lang = Translations::init();
        $target = new TargetSearch($lang);
        $this->expectException(UploadException::class);
        Keys\AKey::getVariant($lang, $target, 0);
        $this->expectExceptionMessageMatches('KEY VARIANT NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testSharedFail(): void
    {
        $lang = Translations::init();
        $lib = new Keys\Random($lang, new TargetSearch($lang));
        $this->expectException(UploadException::class);
        $lib->getSharedKey(); // no key set!
        $this->expectExceptionMessageMatches('SHARED KEY IS EMPTY');
    }

    /**
     * @throws UploadException
     */
    public function testRandom(): void
    {
        $this->assertEquals('aaaaaaa', Keys\Random::generateRandomText(7, ['a','a','a','a']));

        $lang = Translations::init();
        $lib = new Keys\Random($lang, new TargetSearch($lang));
        $this->assertEquals('abcdefghi' . TargetSearch::FILE_DRIVER_SUFF, $lib->fromSharedKey('abcdefghi'));
    }
}
