<?php

namespace TargetTests\Local\DrivingFile\KeyModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers;
use kalanis\UploadPerPartes\UploadException;


class FactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new KeyModifiers\Factory();
        $this->assertInstanceOf(KeyModifiers\Clear::class, $factory->getKeyModifier(KeyModifiers\Factory::VARIANT_CLEAR));
        $this->assertInstanceOf(KeyModifiers\Base64::class, $factory->getKeyModifier(KeyModifiers\Factory::VARIANT_BASE64));
        $this->assertInstanceOf(KeyModifiers\Hex::class, $factory->getKeyModifier(KeyModifiers\Factory::VARIANT_HEX));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(KeyModifiers\Base64::class, $factory->getKeyModifier(KeyModifiers\Base64::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(KeyModifiers\Hex::class, $factory->getKeyModifier(new KeyModifiers\Hex()));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new KeyModifiers\Factory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data key modifier variant is not set!');
        $factory->getKeyModifier(999);
    }

    /**
     * @throws UploadException
     */
    public function testInitClassFail(): void
    {
        $factory = new KeyModifiers\Factory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data key modifier variant is set in a wrong way. Cannot determine it. *stdClass*');
        $factory->getKeyModifier(new \stdClass());
    }

    /**
     * @throws UploadException
     */
    public function testClassAbstractFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data key modifier variant is set in a wrong way. Cannot determine it. *TargetTests\Local\DrivingFile\KeyModifiers\AXstdClass*');
        $factory->getKeyModifier(AXstdClass::class);
    }

    /**
     * @throws UploadException
     */
    public function testClassNotExistsFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Class "this-class-does-not-exists" does not exist');
        $factory->getKeyModifier(999);
    }
}


class XFactory extends KeyModifiers\Factory
{
    protected array $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class AXstdClass extends KeyModifiers\AModifier
{
}
