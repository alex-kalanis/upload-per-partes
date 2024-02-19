<?php

namespace ServerKeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerKeys;
use kalanis\UploadPerPartes\Uploader\Translations;


class KeysTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $lang = new Translations();
        $lib = new ServerKeys\Factory($lang);
        $this->assertInstanceOf(ServerKeys\Volume::class, $lib->getVariant(ServerKeys\Factory::VARIANT_VOLUME));
        $this->assertInstanceOf(ServerKeys\Redis::class, $lib->getVariant(ServerKeys\Factory::VARIANT_REDIS));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lang = new Translations();
        $lib = new ServerKeys\Factory($lang);
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


class XFactory extends ServerKeys\Factory
{
    protected $map = [
        10 => '\stdClass',
        333 => WithConstructParamsNoInterface::class,
        999 => 'this class does not exists',
    ];
}
