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
        $lang = Translations::init();
        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Text', InfoFormat\AFormat::getFormat($lang, InfoFormat\AFormat::FORMAT_TEXT));
        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Json', InfoFormat\AFormat::getFormat($lang, InfoFormat\AFormat::FORMAT_JSON));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $this->expectException(UploadException::class);
        InfoFormat\AFormat::getFormat(Translations::init(), 0);
        $this->expectExceptionMessageMatches('DRIVEFILE VARIANT NOT SET');
    }
}