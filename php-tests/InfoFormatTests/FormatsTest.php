<?php

namespace InfoFormatTests;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Uploader\Translations;


class FormatsTest extends AFormats
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new InfoFormat\Factory(new Translations());
        $this->assertInstanceOf(InfoFormat\Text::class, $factory->getFormat(InfoFormat\Factory::FORMAT_TEXT));
        $this->assertInstanceOf(InfoFormat\Json::class, $factory->getFormat(InfoFormat\Factory::FORMAT_JSON));
        $this->assertInstanceOf(InfoFormat\Line::class, $factory->getFormat(InfoFormat\Factory::FORMAT_LINE));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory(new Translations());
        $this->assertInstanceOf(InfoFormat\Line::class, $factory->getFormat(InfoFormat\Line::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XFactory(new Translations());
        $this->assertInstanceOf(InfoFormat\Line::class, $factory->getFormat(new InfoFormat\Line()));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new InfoFormat\Factory(new Translations());
        $this->expectExceptionMessage('DRIVEFILE VARIANT NOT SET');
        $this->expectException(UploadException::class);
        $factory->getFormat(0);
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $factory = new XFactory();
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $factory->getFormat(10);
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $factory = new XFactory();
        $this->expectExceptionMessage('Class this-class-does-not-exists does not exist');
        $this->expectException(UploadException::class);
        $factory->getFormat(999);
    }

    /**
     * @throws UploadException
     */
    public function testAbstractClassDie(): void
    {
        $factory = new XFactory();
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $factory->getFormat(XstdClass::class);
    }
}


class XFactory extends InfoFormat\Factory
{
    protected $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class XstdClass
{}
