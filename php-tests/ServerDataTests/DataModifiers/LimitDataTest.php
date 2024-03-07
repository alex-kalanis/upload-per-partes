<?php

namespace ServerDataTests\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\DataModifiers;
use kalanis\UploadPerPartes\Uploader\Translations;


class LimitDataTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new DataModifiers\LimitDataFactory(new Translations());
        $this->assertInstanceOf(DataModifiers\Name::class, $factory->getVariant(DataModifiers\LimitDataFactory::VARIANT_NAME));
        $this->assertInstanceOf(DataModifiers\FullPath::class, $factory->getVariant(DataModifiers\LimitDataFactory::VARIANT_FULL_PATH));
        $this->assertInstanceOf(DataModifiers\SaltedName::class, $factory->getVariant(DataModifiers\LimitDataFactory::VARIANT_SALTED_NAME));
        $this->assertInstanceOf(DataModifiers\SaltedFullPath::class, $factory->getVariant(DataModifiers\LimitDataFactory::VARIANT_SALTED_FULL));
        $this->assertInstanceOf(DataModifiers\Serialize::class, $factory->getVariant(DataModifiers\LimitDataFactory::VARIANT_SERIALIZE));
        $this->assertInstanceOf(DataModifiers\Json::class, $factory->getVariant(DataModifiers\LimitDataFactory::VARIANT_JSON));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XLimFactory(new Translations());
        $this->assertInstanceOf(DataModifiers\FullPath::class, $factory->getVariant(DataModifiers\FullPath::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XLimFactory(new Translations());
        $this->assertInstanceOf(DataModifiers\FullPath::class, $factory->getVariant(new DataModifiers\FullPath()));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new DataModifiers\LimitDataFactory(new Translations());
        $this->expectExceptionMessage('KEY MODIFIER NOT SET');
        $this->expectException(UploadException::class);
        $factory->getVariant(0);
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $factory = new XLimFactory();
        $this->expectExceptionMessage('Class stdClass does not have a constructor, so you cannot pass any constructor arguments');
        $this->expectException(UploadException::class);
        $factory->getVariant(10);
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $factory = new XLimFactory();
        $this->expectExceptionMessage('Class this-class-does-not-exists does not exist');
        $this->expectException(UploadException::class);
        $factory->getVariant(999);
    }

    /**
     * @throws UploadException
     */
    public function testAbstractClassDie(): void
    {
        $factory = new XLimFactory();
        $this->expectExceptionMessage('KEY MODIFIER IS WRONG');
        $this->expectException(UploadException::class);
        $factory->getVariant(AInfXstdClass::class);
    }

    /**
     * @throws UploadException
     */
    public function testNonInstanceClassDie(): void
    {
        $factory = new XLimFactory();
        $this->expectExceptionMessage('KEY MODIFIER IS WRONG');
        $this->expectException(UploadException::class);
        $factory->getVariant(XInfStdClass::class);
    }
}


class XLimFactory extends DataModifiers\LimitDataFactory
{
    protected $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class AXLimStdClass
{
    public function __construct($param)
    {
    }
}


class XLimStdClass
{
    public function __construct($param)
    {
    }
}
