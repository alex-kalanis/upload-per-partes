<?php

namespace TargetTests\Local\DrivingFile\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers;
use kalanis\UploadPerPartes\UploadException;


class FactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new DataModifiers\Factory();
        $this->assertInstanceOf(DataModifiers\Clear::class, $factory->getDataModifier(DataModifiers\Factory::VARIANT_CLEAR));
        $this->assertInstanceOf(DataModifiers\Base64::class, $factory->getDataModifier(DataModifiers\Factory::VARIANT_BASE64));
        $this->assertInstanceOf(DataModifiers\Hex::class, $factory->getDataModifier(DataModifiers\Factory::VARIANT_HEX));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(DataModifiers\Base64::class, $factory->getDataModifier(DataModifiers\Base64::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(DataModifiers\Hex::class, $factory->getDataModifier(new DataModifiers\Hex()));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new DataModifiers\Factory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data modifier variant is not set!');
        $factory->getDataModifier(999);
    }

    /**
     * @throws UploadException
     */
    public function testInitClassFail(): void
    {
        $factory = new DataModifiers\Factory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data modifier is set in a wrong way. Cannot determine it. *stdClass*');
        $factory->getDataModifier(new \stdClass());
    }

    /**
     * @throws UploadException
     */
    public function testClassAbstractFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data modifier is set in a wrong way. Cannot determine it. *TargetTests\Local\DrivingFile\DataModifiers\AXstdClass*');
        $factory->getDataModifier(AXstdClass::class);
    }

    /**
     * @throws UploadException
     */
    public function testClassNotExistsFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Class "this-class-does-not-exists" does not exist');
        $factory->getDataModifier(999);
    }
}


class XFactory extends DataModifiers\Factory
{
    protected array $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class AXstdClass extends DataModifiers\AModifier
{
}
