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
        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Text', InfoFormat\Factory::getFormat($lang, InfoFormat\Factory::FORMAT_TEXT));
        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Json', InfoFormat\Factory::getFormat($lang, InfoFormat\Factory::FORMAT_JSON));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $this->expectException(UploadException::class);
        InfoFormat\Factory::getFormat(new Translations(), 0);
        $this->expectExceptionMessageMatches('DRIVEFILE VARIANT NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $this->expectException(UploadException::class);
        XFactory::getFormat(new Translations(), 10);
        $this->expectExceptionMessageMatches('DRIVEFILE VARIANT IS WRONG');
    }
}


class XFactory extends InfoFormat\Factory
{
    protected static $map = [
        10 => '\stdClass',
    ];
}
