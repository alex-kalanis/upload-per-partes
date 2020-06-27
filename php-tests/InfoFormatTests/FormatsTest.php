<?php

namespace InfoFormatTests;

use UploadPerPartes\InfoFormat;
use UploadPerPartes\Uploader\Translations;

class FormatsTest extends AFormats
{
    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testInit(): void
    {
        $lang = Translations::init();
        $this->assertInstanceOf('\UploadPerPartes\InfoFormat\Text', InfoFormat\AFormat::getFormat($lang, InfoFormat\AFormat::FORMAT_TEXT));
        $this->assertInstanceOf('\UploadPerPartes\InfoFormat\Json', InfoFormat\AFormat::getFormat($lang, InfoFormat\AFormat::FORMAT_JSON));
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage DRIVEFILE VARIANT NOT SET
     */
    public function testInitFail(): void
    {
        InfoFormat\AFormat::getFormat(Translations::init(), 0);
    }
}