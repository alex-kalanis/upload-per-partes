<?php

namespace ServerDataTests\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData;
use kalanis\UploadPerPartes\Uploader\Translations;


class EncodeFactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $lib = new ServerData\KeyModifiers\EncodeFactory(new Translations());
        $this->assertInstanceOf(ServerData\KeyModifiers\Clear::class, $lib->getVariant(ServerData\KeyModifiers\EncodeFactory::VARIANT_CLEAR));
        $this->assertInstanceOf(ServerData\KeyModifiers\Random::class, $lib->getVariant(ServerData\KeyModifiers\EncodeFactory::VARIANT_RANDOM));
        $this->assertInstanceOf(ServerData\KeyModifiers\Base64::class, $lib->getVariant(ServerData\KeyModifiers\EncodeFactory::VARIANT_BASE64));
        $this->assertInstanceOf(ServerData\KeyModifiers\Md5::class, $lib->getVariant(ServerData\KeyModifiers\EncodeFactory::VARIANT_MD5));
        $this->assertInstanceOf(ServerData\KeyModifiers\Hex::class, $lib->getVariant(ServerData\KeyModifiers\EncodeFactory::VARIANT_HEX));

        $this->assertInstanceOf(ServerData\KeyModifiers\Md5::class, $lib->getVariant(new ServerData\KeyModifiers\Md5()));
        $this->assertInstanceOf(ServerData\KeyModifiers\Md5::class, $lib->getVariant(ServerData\KeyModifiers\Md5::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lib = new ServerData\KeyModifiers\EncodeFactory(new Translations());
        $this->expectExceptionMessage('KEY VARIANT NOT SET');
        $this->expectException(UploadException::class);
        $lib->getVariant(0);
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $lib = new XEncodeFactory(new Translations());
        $this->expectExceptionMessage('KEY VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $lib->getVariant(AEncodeWithConstructParamsNoInterface::class);
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $lib = new XEncodeFactory(new Translations());
        $this->expectExceptionMessage('Class "this class does not exists" does not exist');
        $this->expectException(UploadException::class);
        $lib->getVariant(999);
    }

    /**
     * @throws UploadException
     */
    public function testClassConstructDie(): void
    {
        $lib = new XEncodeFactory(new Translations());
        $this->expectExceptionMessage('KEY VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $lib->getVariant(333);
    }
}


class EncodeWithConstructParamsNoInterface
{
    public function __construct($params)
    {
    }
}


abstract class AEncodeWithConstructParamsNoInterface
{
    public function __construct($params)
    {
    }
}


class XEncodeFactory extends ServerData\KeyModifiers\EncodeFactory
{
    protected array $map = [
        10 => '\stdClass',
        333 => EncodeWithConstructParamsNoInterface::class,
        999 => 'this class does not exists',
    ];
}
