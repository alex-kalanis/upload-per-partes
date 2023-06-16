<?php

namespace TraitsTests;


use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\Uploader\Translations;


class LangTest extends \CommonTestClass
{
    public function testSimple(): void
    {
        $lib = new XLang();
        $this->assertNotEmpty($lib->getUppLang());
        $this->assertInstanceOf(Translations::class, $lib->getUppLang());
        $lib->setUppLang(new XTrans());
        $this->assertInstanceOf(XTrans::class, $lib->getUppLang());
        $lib->setUppLang(null);
        $this->assertInstanceOf(Translations::class, $lib->getUppLang());
    }
}


class XLang
{
    use TLang;
}


class XTrans implements IUPPTranslations
{
    public function uppSentNameIsEmpty(): string
    {
        return 'mock';
    }

    public function uppUploadNameIsEmpty(): string
    {
        return 'mock';
    }

    public function uppSharedKeyIsEmpty(): string
    {
        return 'mock';
    }

    public function uppSharedKeyIsInvalid(): string
    {
        return 'mock';
    }

    public function uppKeyVariantNotSet(): string
    {
        return 'mock';
    }

    public function uppKeyVariantIsWrong(string $className): string
    {
        return 'mock';
    }

    public function uppTargetDirIsEmpty(): string
    {
        return 'mock';
    }

    public function uppDriveFileAlreadyExists(string $driveFile): string
    {
        return 'mock';
    }

    public function uppDriveFileNotContinuous(string $driveFile): string
    {
        return 'mock';
    }

    public function uppDriveFileCannotRemove(string $key): string
    {
        return 'mock';
    }

    public function uppDriveFileVariantNotSet(): string
    {
        return 'mock';
    }

    public function uppDriveDataNotSet(): string
    {
        return 'mock';
    }

    public function uppDriveFileVariantIsWrong(string $className): string
    {
        return 'mock';
    }

    public function uppDriveFileCannotRead(string $key): string
    {
        return 'mock';
    }

    public function uppDriveFileCannotWrite(string $key): string
    {
        return 'mock';
    }

    public function uppCannotRemoveData(string $location): string
    {
        return 'mock';
    }

    public function uppReadTooEarly(string $key): string
    {
        return 'mock';
    }

    public function uppCannotOpenFile(string $location): string
    {
        return 'mock';
    }

    public function uppCannotReadFile(string $location): string
    {
        return 'mock';
    }

    public function uppCannotSeekFile(string $location): string
    {
        return 'mock';
    }

    public function uppCannotWriteFile(string $location): string
    {
        return 'mock';
    }

    public function uppCannotTruncateFile(string $location): string
    {
        return 'mock';
    }

    public function uppSegmentOutOfBounds(int $segment): string
    {
        return 'mock';
    }

    public function uppSegmentNotUploadedYet(int $segment): string
    {
        return 'mock';
    }
}
