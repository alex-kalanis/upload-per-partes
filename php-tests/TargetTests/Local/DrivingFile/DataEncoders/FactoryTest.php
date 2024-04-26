<?php

namespace TargetTests\Local\DrivingFile\DataEncoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


class FactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new DataEncoders\Factory();
        $this->assertInstanceOf(DataEncoders\Text::class, $factory->getDataEncoder(new Config(['data_encoder' => DataEncoders\Factory::FORMAT_TEXT])));
        $this->assertInstanceOf(DataEncoders\Json::class, $factory->getDataEncoder(new Config(['data_encoder' => DataEncoders\Factory::FORMAT_JSON])));
        $this->assertInstanceOf(DataEncoders\Line::class, $factory->getDataEncoder(new Config(['data_encoder' => DataEncoders\Factory::FORMAT_LINE])));
        $this->assertInstanceOf(DataEncoders\Serialize::class, $factory->getDataEncoder(new Config(['data_encoder' => DataEncoders\Factory::FORMAT_SERIAL])));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(DataEncoders\Text::class, $factory->getDataEncoder(new Config(['data_encoder' => DataEncoders\Text::class])));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(DataEncoders\Json::class, $factory->getDataEncoder(new Config(['data_encoder' => new DataEncoders\Json()])));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new DataEncoders\Factory();
        $conf = new Config([]);
        $conf->dataEncoder = 999;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data encoder variant is not set!');
        $factory->getDataEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testInitClassFail(): void
    {
        $factory = new DataEncoders\Factory();
        $conf = new Config([]);
        $conf->dataEncoder = new \stdClass();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data encoder is set in a wrong way. Cannot determine it. *stdClass*');
        $factory->getDataEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testClassAbstractFail(): void
    {
        $factory = new XFactory();
        $conf = new Config([]);
        $conf->dataEncoder = AXstdClass::class;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data encoder is set in a wrong way. Cannot determine it. *TargetTests\Local\DrivingFile\DataEncoders\AXstdClass*');
        $factory->getDataEncoder($conf);
    }

    /**
     * @throws UploadException
     */
    public function testClassNotExistsFail(): void
    {
        $factory = new XFactory();
        $conf = new Config([]);
        $conf->dataEncoder = 999;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Class "this-class-does-not-exists" does not exist');
        $factory->getDataEncoder($conf);
    }
}


class XFactory extends DataEncoders\Factory
{
    protected array $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class AXstdClass extends DataEncoders\AEncoder
{
}
