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
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->assertInstanceOf(Keys\SimpleVolume::class, Keys\Factory::getVariant($target, Keys\Factory::VARIANT_VOLUME, $lang));
        $this->assertInstanceOf(Keys\Random::class, Keys\Factory::getVariant($target, Keys\Factory::VARIANT_RANDOM, $lang));
        $this->assertInstanceOf(Keys\Redis::class, Keys\Factory::getVariant($target, Keys\Factory::VARIANT_REDIS, $lang));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lang = new Translations();
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->expectException(UploadException::class);
        Keys\Factory::getVariant($target, 0, $lang);
        $this->expectExceptionMessageMatches('KEY VARIANT NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $lang = new Translations();
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->expectException(UploadException::class);
        XFactory::getVariant($target, 10, $lang);
        $this->expectExceptionMessageMatches('KEY VARIANT IS WRONG');
    }

    /**
     * @throws UploadException
     */
    public function testSharedFail(): void
    {
        $lang = new Translations();
        $lib = new Keys\Random(new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang), $lang);
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
        $lib = new Keys\Random(new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang), $lang);
        $this->assertEquals('abcdefghi' . TargetSearch::FILE_DRIVER_SUFF, $lib->fromSharedKey('abcdefghi'));
    }
}


class XFactory extends Keys\Factory
{
    protected static $map = [
        10 => '\stdClass',
    ];
}
