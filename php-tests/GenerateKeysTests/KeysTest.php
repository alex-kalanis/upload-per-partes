<?php

namespace GenerateKeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\GenerateKeys;
use kalanis\UploadPerPartes\Uploader\Translations;


class KeysTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $lang = new Translations();
        $lib = new GenerateKeys\Factory($lang);
        $this->assertInstanceOf(GenerateKeys\Clear::class, $lib->getVariant(GenerateKeys\Factory::VARIANT_CLEAR));
        $this->assertInstanceOf(GenerateKeys\Random::class, $lib->getVariant(GenerateKeys\Factory::VARIANT_RANDOM));
        $this->assertInstanceOf(GenerateKeys\Base64::class, $lib->getVariant(GenerateKeys\Factory::VARIANT_BASE64));
        $this->assertInstanceOf(GenerateKeys\Md5::class, $lib->getVariant(GenerateKeys\Factory::VARIANT_MD5));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lang = new Translations();
        $lib = new GenerateKeys\Factory($lang);
        $this->expectException(UploadException::class);
        $lib->getVariant(0);
        $this->expectExceptionMessageMatches('KEY VARIANT NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $lang = new Translations();
        $lib = new XFactory($lang);
        $this->expectException(UploadException::class);
        $lib->getVariant(10);
        $this->expectExceptionMessageMatches('KEY VARIANT IS WRONG');
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $lang = new Translations();
        $lib = new XFactory($lang);
        $this->expectException(UploadException::class);
        $lib->getVariant(999);
        $this->expectExceptionMessageMatches('KEY VARIANT IS WRONG');
    }

    /**
     * @throws UploadException
     */
    public function testClassConstructDie(): void
    {
        $lang = new Translations();
        $lib = new XFactory($lang);
        $this->expectException(UploadException::class);
        $lib->getVariant(333);
        $this->expectExceptionMessageMatches('KEY VARIANT IS WRONG');
    }
}


class WithConstructParamsNoInterface
{
    public function __construct()
    {
    }
}


class XFactory extends GenerateKeys\Factory
{
    protected $map = [
        10 => '\stdClass',
        333 => WithConstructParamsNoInterface::class,
        999 => 'this class does not exists',
    ];
}
