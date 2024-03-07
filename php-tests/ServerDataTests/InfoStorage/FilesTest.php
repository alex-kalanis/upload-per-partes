<?php

namespace ServerDataTests\InfoStorage;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;


class FilesTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoPass();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertTrue($storage->exists($file));
        $storage->load($file);
        $storage->remove($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectExceptionMessage('CANNOT READ FILE');
        $this->expectException(UploadException::class);
        $storage->load($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable2(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectExceptionMessage('CANNOT READ FILE');
        $this->expectException(UploadException::class);
        $storage->load($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnwrittable(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectExceptionMessage('CANNOT WRITE DRIVEFILE');
        $this->expectException(UploadException::class);
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
    }

    /**
     * @throws UploadException
     */
    public function testUnwrittable2(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectExceptionMessage('CANNOT WRITE DRIVEFILE');
        $this->expectException(UploadException::class);
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
    }

    /**
     * @throws UploadException
     */
    public function testDeleted(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectExceptionMessage('DRIVEFILE CANNOT BE REMOVED');
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
    }

    /**
     * @throws UploadException
     */
    public function testDeleted2(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectExceptionMessage('DRIVEFILE CANNOT BE REMOVED');
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
    }

    /**
     * @throws UploadException
     */
    public function testExistsDied(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectExceptionMessage('mock');
        $this->expectException(UploadException::class);
        $storage->exists($file); // dies here
    }

    /**
     * @throws UploadException
     */
    public function testClassPass(): void
    {
        $this->assertTrue((new \FilesTrait())->mockFilesInfoPass()->checkKeyClasses(
            new XFilesModifyPass(),
            new XFilesEncodePass(),
            new XFilesFormatPass()
        ));
    }

    /**
     * @throws UploadException
     */
    public function testClassFailStorageKeyModifier(): void
    {
        $this->expectExceptionMessage('KEY MODIFIER IS WRONG');
        $this->expectException(UploadException::class);
        (new \FilesTrait())->mockFilesInfoPass()->checkKeyClasses(
            new XFilesModifyFail(),
            new XFilesEncodePass(),
            new XFilesFormatPass()
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailStorageKeyVariant(): void
    {
        $this->expectExceptionMessage('KEY VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        (new \FilesTrait())->mockFilesInfoPass()->checkKeyClasses(
            new XFilesModifyPass(),
            new XFilesEncodeFail(),
            new XFilesFormatPass()
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailDataFormat(): void
    {
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        (new \FilesTrait())->mockFilesInfoPass()->checkKeyClasses(
            new XFilesModifyPass(),
            new XFilesEncodePass(),
            new XFilesFormatFail()
        );
    }
}


class XFilesModifyFail implements Interfaces\ILimitPassedData
{
    public function getLimitedData(ServerData\Data $data): string
    {
        return 'mock';
    }
}


class XFilesModifyPass extends XFilesModifyFail implements Interfaces\InfoStorage\ForFiles
{}


class XFilesEncodeFail implements Interfaces\IEncodeSharedKey
{
    public function pack(string $data): string
    {
        return 'mock';
    }

    public function unpack(string $data): string
    {
        return 'mock';
    }
}


class XFilesEncodePass extends XFilesEncodeFail implements Interfaces\InfoStorage\ForFiles
{}


class XFilesFormatFail implements Interfaces\IInfoFormatting
{
    public function fromFormat(string $content): ServerData\Data
    {
        return new ServerData\Data();
    }

    public function toFormat(ServerData\Data $data): string
    {
        return 'mock';
    }
}


class XFilesFormatPass extends XFilesFormatFail implements Interfaces\InfoStorage\ForFiles
{}
