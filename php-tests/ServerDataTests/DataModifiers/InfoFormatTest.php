<?php

namespace ServerDataTests\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\DataModifiers;
use kalanis\UploadPerPartes\Uploader\Translations;


class InfoFormatTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new DataModifiers\InfoFormatFactory(new Translations());
        $this->assertInstanceOf(DataModifiers\Text::class, $factory->getFormat(DataModifiers\InfoFormatFactory::FORMAT_TEXT));
        $this->assertInstanceOf(DataModifiers\Json::class, $factory->getFormat(DataModifiers\InfoFormatFactory::FORMAT_JSON));
        $this->assertInstanceOf(DataModifiers\Line::class, $factory->getFormat(DataModifiers\InfoFormatFactory::FORMAT_LINE));
        $this->assertInstanceOf(DataModifiers\Serialize::class, $factory->getFormat(DataModifiers\InfoFormatFactory::FORMAT_SERIAL));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XInfFactory(new Translations());
        $this->assertInstanceOf(DataModifiers\Line::class, $factory->getFormat(DataModifiers\Line::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XInfFactory(new Translations());
        $this->assertInstanceOf(DataModifiers\Line::class, $factory->getFormat(new DataModifiers\Line()));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new DataModifiers\InfoFormatFactory(new Translations());
        $this->expectExceptionMessage('DRIVEFILE VARIANT NOT SET');
        $this->expectException(UploadException::class);
        $factory->getFormat(0);
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $factory = new XInfFactory();
        $this->expectExceptionMessage('Class stdClass does not have a constructor, so you cannot pass any constructor arguments');
        $this->expectException(UploadException::class);
        $factory->getFormat(10);
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $factory = new XInfFactory();
        $this->expectExceptionMessage('Class "this-class-does-not-exists" does not exist');
        $this->expectException(UploadException::class);
        $factory->getFormat(999);
    }

    /**
     * @throws UploadException
     */
    public function testAbstractClassDie(): void
    {
        $factory = new XInfFactory();
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $factory->getFormat(AInfXstdClass::class);
    }

    /**
     * @throws UploadException
     */
    public function testNonInstanceClassDie(): void
    {
        $factory = new XInfFactory();
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $factory->getFormat(XInfStdClass::class);
    }
}


class XInfFactory extends DataModifiers\InfoFormatFactory
{
    protected array $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class AInfXstdClass
{
    public function __construct($param)
    {
    }
}


class XInfStdClass
{
    public function __construct($param)
    {
    }
}
