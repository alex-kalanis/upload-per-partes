<?php

namespace TargetTests\Local\TemporaryStorage\KeyEncoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders;
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
        $this->assertInstanceOf(KeyEncoders\FullPath::class, $factory->getKeyEncoder(new Config(['temp_encoder' => KeyEncoders\Factory::FORMAT_FULL])));
        $this->assertInstanceOf(KeyEncoders\Name::class, $factory->getKeyEncoder(new Config(['temp_encoder' => KeyEncoders\Factory::FORMAT_NAME])));
        $this->assertInstanceOf(KeyEncoders\Random::class, $factory->getKeyEncoder(new Config(['temp_encoder' => KeyEncoders\Factory::FORMAT_RANDOM])));
        $this->assertInstanceOf(KeyEncoders\SaltedFullPath::class, $factory->getKeyEncoder(new Config(['temp_encoder' => KeyEncoders\Factory::FORMAT_SALTED_FULL])));
        $this->assertInstanceOf(KeyEncoders\SaltedName::class, $factory->getKeyEncoder(new Config(['temp_encoder' => KeyEncoders\Factory::FORMAT_SALTED_NAME])));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(KeyEncoders\Name::class, $factory->getKeyEncoder(new Config(['temp_encoder' => KeyEncoders\Name::class])));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(KeyEncoders\Random::class, $factory->getKeyEncoder(new Config(['temp_encoder' => new KeyEncoders\Random()])));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new KeyEncoders\Factory();
        $conf = new Config([]);
        $conf->temporaryEncoder = 999;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The temporary storage encoder variant is not set!');
        $factory->getKeyEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testInitClassFail(): void
    {
        $factory = new KeyEncoders\Factory();
        $conf = new Config([]);
        $conf->temporaryEncoder = new \stdClass();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The temporary storage encoder variant is set in a wrong way. Cannot determine it. *stdClass*');
        $factory->getKeyEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testClassAbstractFail(): void
    {
        $factory = new XFactory();
        $conf = new Config([]);
        $conf->temporaryEncoder = AXstdClass::class;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The temporary storage encoder variant is set in a wrong way. Cannot determine it. *TargetTests\Local\TemporaryStorage\KeyEncoders\AXstdClass*');
        $factory->getKeyEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testClassNotExistsFail(): void
    {
        $factory = new XFactory();
        $conf = new Config([]);
        $conf->temporaryEncoder = 999;
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
