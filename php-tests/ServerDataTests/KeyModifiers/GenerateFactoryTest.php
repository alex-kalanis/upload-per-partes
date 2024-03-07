<?php

namespace ServerDataTests\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData;
use kalanis\UploadPerPartes\Uploader\Translations;


class GenerateFactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $lib = new ServerData\KeyModifiers\GenerateFactory(new Translations());
        $this->assertInstanceOf(ServerData\KeyModifiers\Clear::class, $lib->getVariant(ServerData\KeyModifiers\GenerateFactory::VARIANT_CLEAR));
        $this->assertInstanceOf(ServerData\KeyModifiers\Random::class, $lib->getVariant(ServerData\KeyModifiers\GenerateFactory::VARIANT_RANDOM));
        $this->assertInstanceOf(ServerData\KeyModifiers\Base64::class, $lib->getVariant(ServerData\KeyModifiers\GenerateFactory::VARIANT_BASE64));
        $this->assertInstanceOf(ServerData\KeyModifiers\Md5::class, $lib->getVariant(ServerData\KeyModifiers\GenerateFactory::VARIANT_MD5));
        $this->assertInstanceOf(ServerData\KeyModifiers\Hex::class, $lib->getVariant(ServerData\KeyModifiers\GenerateFactory::VARIANT_HEX));

        $this->assertInstanceOf(ServerData\KeyModifiers\Md5::class, $lib->getVariant(new ServerData\KeyModifiers\Md5()));
        $this->assertInstanceOf(ServerData\KeyModifiers\Md5::class, $lib->getVariant(ServerData\KeyModifiers\Md5::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lib = new ServerData\KeyModifiers\GenerateFactory(new Translations());
        $this->expectExceptionMessage('KEY VARIANT NOT SET');
        $this->expectException(UploadException::class);
        $lib->getVariant(0);
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $lib = new XGenerateFactory(new Translations());
        $this->expectExceptionMessage('KEY VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $lib->getVariant(AGenerateithConstructParamsNoInterface::class);
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $lib = new XGenerateFactory(new Translations());
        $this->expectExceptionMessage('Class this class does not exists does not exist');
        $this->expectException(UploadException::class);
        $lib->getVariant(999);
    }

    /**
     * @throws UploadException
     */
    public function testClassConstructDie(): void
    {
        $lib = new XGenerateFactory(new Translations());
        $this->expectExceptionMessage('KEY VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $lib->getVariant(333);
    }
}


class GenerateWithConstructParamsNoInterface
{
    public function __construct($params)
    {
    }
}


abstract class AGenerateithConstructParamsNoInterface
{
    public function __construct($params)
    {
    }
}


class XGenerateFactory extends ServerData\KeyModifiers\GenerateFactory
{
    protected $map = [
        10 => '\stdClass',
        333 => GenerateWithConstructParamsNoInterface::class,
        999 => 'this class does not exists',
    ];
}
