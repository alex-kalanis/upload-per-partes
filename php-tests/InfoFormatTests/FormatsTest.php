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
        $lang = new Translations();
        $this->assertInstanceOf(InfoFormat\Text::class, InfoFormat\Factory::getFormat(InfoFormat\Factory::FORMAT_TEXT, $lang));
        $this->assertInstanceOf(InfoFormat\Json::class, InfoFormat\Factory::getFormat(InfoFormat\Factory::FORMAT_JSON, $lang));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $this->expectException(UploadException::class);
        InfoFormat\Factory::getFormat(0, new Translations());
        $this->expectExceptionMessageMatches('DRIVEFILE VARIANT NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $this->expectException(UploadException::class);
        XFactory::getFormat(10, new Translations());
        $this->expectExceptionMessageMatches('DRIVEFILE VARIANT IS WRONG');
    }
}


class XFactory extends InfoFormat\Factory
{
    protected static $map = [
        10 => \stdClass::class,
    ];
}
