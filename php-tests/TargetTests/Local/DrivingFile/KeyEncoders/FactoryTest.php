<?php

namespace TargetTests\Local\DrivingFile\KeyEncoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


class FactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new KeyEncoders\Factory();
        $this->assertInstanceOf(KeyEncoders\FullPath::class, $factory->getKeyEncoder(new Config(['key_encoder' => KeyEncoders\Factory::VARIANT_FULL_PATH])));
        $this->assertInstanceOf(KeyEncoders\Json::class, $factory->getKeyEncoder(new Config(['key_encoder' => KeyEncoders\Factory::VARIANT_JSON])));
        $this->assertInstanceOf(KeyEncoders\Name::class, $factory->getKeyEncoder(new Config(['key_encoder' => KeyEncoders\Factory::VARIANT_NAME])));
        $this->assertInstanceOf(KeyEncoders\SaltedFullPath::class, $factory->getKeyEncoder(new Config(['key_encoder' => KeyEncoders\Factory::VARIANT_SALTED_FULL])));
        $this->assertInstanceOf(KeyEncoders\SaltedName::class, $factory->getKeyEncoder(new Config(['key_encoder' => KeyEncoders\Factory::VARIANT_SALTED_NAME])));
        $this->assertInstanceOf(KeyEncoders\Serialize::class, $factory->getKeyEncoder(new Config(['key_encoder' => KeyEncoders\Factory::VARIANT_SERIALIZE])));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(KeyEncoders\Name::class, $factory->getKeyEncoder(new Config(['key_encoder' => KeyEncoders\Name::class])));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(KeyEncoders\Json::class, $factory->getKeyEncoder(new Config(['key_encoder' => new KeyEncoders\Json()])));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new KeyEncoders\Factory();
        $conf = new Config([]);
        $conf->keyEncoder = 999;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data key encoder variant is not set!');
        $factory->getKeyEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testInitClassFail(): void
    {
        $factory = new KeyEncoders\Factory();
        $conf = new Config([]);
        $conf->keyEncoder = new \stdClass();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data key encoder variant is set in a wrong way. Cannot determine it. *stdClass*');
        $factory->getKeyEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testClassAbstractFail(): void
    {
        $factory = new XFactory();
        $conf = new Config([]);
        $conf->keyEncoder = AXstdClass::class;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data key encoder variant is set in a wrong way. Cannot determine it. *TargetTests\Local\DrivingFile\KeyEncoders\AXstdClass*');
        $factory->getKeyEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testClassNotExistsFail(): void
    {
        $factory = new XFactory();
        $conf = new Config([]);
        $conf->keyEncoder = 999;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Class "this-class-does-not-exists" does not exist');
        $factory->getKeyEncoder($conf);
    }
}


class XFactory extends KeyEncoders\Factory
{
    protected array $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class AXstdClass extends KeyEncoders\AEncoder
{
}
