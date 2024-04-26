<?php

namespace TraitsTests;


use kalanis\UploadPerPartes\Traits;
use kalanis\UploadPerPartes\Uploader\Translations;


class LangTest extends \CommonTestClass
{
    public function testSimple(): void
    {
        $lib = new XLang();
        $this->assertNotEmpty($lib->getUppLang());
        $this->assertInstanceOf(Translations::class, $lib->getUppLang());
        $lib->setUppLang(new \XTrans());
        $this->assertInstanceOf(\XTrans::class, $lib->getUppLang());
        $lib->setUppLang(null);
        $this->assertInstanceOf(Translations::class, $lib->getUppLang());
    }

    public function testInit(): void
    {
        $lib = new XLangInit(new \XTrans());
        $this->assertInstanceOf(\XTrans::class, $lib->getUppLang());
    }
}


class XLang
{
    use Traits\TLang;
}


class XLangInit
{
    use Traits\TLangInit;
}
