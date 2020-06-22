<?php

namespace FormatTests;

use UploadPerPartes\DataFormat;
use UploadPerPartes\Uploader\Translations;

class FormatsTest extends AFormats
{
    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testInit()
    {
        $lang = Translations::init();
        $this->assertInstanceOf('\UploadPerPartes\DataFormat\Text', DataFormat\AFormat::getFormat($lang, DataFormat\AFormat::FORMAT_TEXT));
        $this->assertInstanceOf('\UploadPerPartes\DataFormat\Json', DataFormat\AFormat::getFormat($lang, DataFormat\AFormat::FORMAT_JSON));
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage DRIVEFILE VARIANT NOT SET
     */
    public function testInitFail()
    {
        DataFormat\AFormat::getFormat(Translations::init(), 0);
    }
}