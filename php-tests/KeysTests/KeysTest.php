<?php

namespace KeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Keys;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use kalanis\UploadPerPartes\Uploader\Translations;
use Support;


class KeysTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $lang = new Translations();
        $target = new TargetSearch($lang, new Support\InfoRam($lang), new Support\DataRam($lang));
        $this->assertInstanceOf('\kalanis\UploadPerPartes\Keys\SimpleVolume', Keys\Factory::getVariant($lang, $target, Keys\Factory::VARIANT_VOLUME));
        $this->assertInstanceOf('\kalanis\UploadPerPartes\Keys\Random', Keys\Factory::getVariant($lang, $target, Keys\Factory::VARIANT_RANDOM));
        $this->assertInstanceOf('\kalanis\UploadPerPartes\Keys\Redis', Keys\Factory::getVariant($lang, $target, Keys\Factory::VARIANT_REDIS));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lang = new Translations();
        $target = new TargetSearch($lang, new Support\InfoRam($lang), new Support\DataRam($lang));
        $this->expectException(UploadException::class);
        Keys\Factory::getVariant($lang, $target, 0);
        $this->expectExceptionMessageMatches('KEY VARIANT NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $lang = new Translations();
        $target = new TargetSearch($lang, new Support\InfoRam($lang), new Support\DataRam($lang));
        $this->expectException(UploadException::class);
        XFactory::getVariant($lang, $target, 10);
        $this->expectExceptionMessageMatches('KEY VARIANT IS WRONG');
    }

    /**
     * @throws UploadException
     */
    public function testSharedFail(): void
    {
        $lang = new Translations();
        $lib = new Keys\Random($lang, new TargetSearch($lang, new Support\InfoRam($lang), new Support\DataRam($lang)));
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

        $lang = new Translations();
        $lib = new Keys\Random($lang, new TargetSearch($lang, new Support\InfoRam($lang), new Support\DataRam($lang)));
        $this->assertEquals('abcdefghi' . TargetSearch::FILE_DRIVER_SUFF, $lib->fromSharedKey('abcdefghi'));
    }
}


class XFactory extends Keys\Factory
{
    protected static $map = [
        10 => '\stdClass',
    ];
}
