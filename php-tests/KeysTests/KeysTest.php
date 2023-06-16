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
        $lib = new Keys\Factory($lang);
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->assertInstanceOf(Keys\SimpleVolume::class, $lib->getVariant($target, Keys\Factory::VARIANT_VOLUME));
        $this->assertInstanceOf(Keys\Random::class, $lib->getVariant($target, Keys\Factory::VARIANT_RANDOM));
        $this->assertInstanceOf(Keys\Redis::class, $lib->getVariant($target, Keys\Factory::VARIANT_REDIS));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lang = new Translations();
        $lib = new Keys\Factory($lang);
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->expectException(UploadException::class);
        $lib->getVariant($target, 0);
        $this->expectExceptionMessageMatches('KEY VARIANT NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $lang = new Translations();
        $lib = new XFactory($lang);
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->expectException(UploadException::class);
        $lib->getVariant($target, 10);
        $this->expectExceptionMessageMatches('KEY VARIANT IS WRONG');
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $lang = new Translations();
        $lib = new XFactory($lang);
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->expectException(UploadException::class);
        $lib->getVariant($target, 999);
        $this->expectExceptionMessageMatches('KEY VARIANT IS WRONG');
    }

    /**
     * @throws UploadException
     */
    public function testClassConstructDie(): void
    {
        $lang = new Translations();
        $lib = new XFactory($lang);
        $target = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->expectException(UploadException::class);
        $lib->getVariant($target, 333);
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


class WithConstructParamsNoInterface
{
    public function __construct($param1, $param2)
    {
    }
}


class XFactory extends Keys\Factory
{
    protected static $map = [
        10 => '\stdClass',
        333 => WithConstructParamsNoInterface::class,
        999 => 'this class does not exists',
    ];
}
