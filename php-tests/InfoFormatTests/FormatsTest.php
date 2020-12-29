<?php

namespace InfoFormatTests;


use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Uploader\Translations;


class FormatsTest extends AFormats
{
    /**
     * @throws \kalanis\UploadPerPartes\Exceptions\UploadException
     */
    public function testInit(): void
    {
        $lang = Translations::init();
        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Text', InfoFormat\AFormat::getFormat($lang, InfoFormat\AFormat::FORMAT_TEXT));
        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Json', InfoFormat\AFormat::getFormat($lang, InfoFormat\AFormat::FORMAT_JSON));
    }

    /**
     * @expectedException \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage DRIVEFILE VARIANT NOT SET
     */
    public function testInitFail(): void
    {
        InfoFormat\AFormat::getFormat(Translations::init(), 0);
    }
}