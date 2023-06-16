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
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new InfoFormat\Factory(new Translations());
        $this->expectException(UploadException::class);
        $factory->getFormat(0);
        $this->expectExceptionMessageMatches('DRIVEFILE VARIANT NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $factory->getFormat(10);
        $this->expectExceptionMessageMatches('DRIVEFILE VARIANT IS WRONG');
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $factory->getFormat(999);
        $this->expectExceptionMessageMatches('DRIVEFILE VARIANT IS WRONG');
    }
}


class XFactory extends InfoFormat\Factory
{
    protected static $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}
