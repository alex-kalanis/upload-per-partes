<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Uploader;
use kalanis\UploadPerPartes\UploadException;


class LangTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testSimple(): void
    {
        $lib = new Uploader\LangFactory();
        $lang = $lib->getLang(new Uploader\Config([]));
        $this->assertInstanceOf(Interfaces\IUppTranslations::class, $lang);
    }

    /**
     * @throws UploadException
     */
    public function testBadClassInstance(): void
    {
        $lib = new Uploader\LangFactory('wrong variant 1');
        $conf = new Uploader\Config([]);
        $conf->lang = new \stdClass();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('wrong variant 1');
        $lib->getLang($conf);
    }

    /**
     * @throws UploadException
     */
    public function testBadClassNamed(): void
    {
        $lib = new Uploader\LangFactory('wrong variant 2');
        $conf = new Uploader\Config([]);
        $conf->lang = \stdClass::class;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('wrong variant 2');
        $lib->getLang($conf);
    }

    /**
     * @throws UploadException
     */
    public function testBadClassNotExists(): void
    {
        $lib = new Uploader\LangFactory('wrong variant 3');
        $conf = new Uploader\Config([]);
        $conf->lang = PHP_VERSION_ID > 77000 ? 'class-not-exists' : '"class-not-exists"';
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Class "class-not-exists" does not exist');
        $lib->getLang($conf);
    }

    /**
     * @throws UploadException
     */
    public function testBadClassAbstract(): void
    {
        $lib = new Uploader\LangFactory('wrong variant 4');
        $conf = new Uploader\Config([]);
        $conf->lang = XACls::class;
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('wrong variant 4');
        $lib->getLang($conf);
    }
}


abstract class XACls
{}