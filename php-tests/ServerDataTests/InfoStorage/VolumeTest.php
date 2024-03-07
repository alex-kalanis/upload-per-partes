<?php

namespace ServerDataTests\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;


class VolumeTest extends AStorage
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertTrue($storage->exists($file));
        $storage->load($file);
        $storage->remove($file);
        $this->assertFalse($storage->exists($file));
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $storage->load($file);
        chmod($file, 0333);
        $this->expectExceptionMessage('CANNOT READ DRIVEFILE');
        $this->expectException(UploadException::class);
        $storage->load($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnwriteable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        chmod($file, 0444);
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
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $storage->remove($file);
        $this->expectExceptionMessage('DRIVEFILE CANNOT BE REMOVED');
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
    }

    /**
     * @throws UploadException
     */
    public function testClassPass(): void
    {
        $this->assertTrue($this->mockStorage()->checkKeyClasses(
            new XVolumeModifyPass(),
            new XVolumeEncodePass(),
            new XVolumeFormatPass()
        ));
    }

    /**
     * @throws UploadException
     */
    public function testClassFailStorageKeyModifier(): void
    {
        $this->expectExceptionMessage('KEY MODIFIER IS WRONG');
        $this->expectException(UploadException::class);
        $this->mockStorage()->checkKeyClasses(
            new XVolumeModifyFail(),
            new XVolumeEncodePass(),
            new XVolumeFormatPass()
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailStorageKeyVariant(): void
    {
        $this->expectExceptionMessage('KEY VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $this->mockStorage()->checkKeyClasses(
            new XVolumeModifyPass(),
            new XVolumeEncodeFail(),
            new XVolumeFormatPass()
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailDataFormat(): void
    {
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $this->mockStorage()->checkKeyClasses(
            new XVolumeModifyPass(),
            new XVolumeEncodePass(),
            new XVolumeFormatFail()
        );
    }
}


class XVolumeModifyFail implements Interfaces\ILimitPassedData
{
    public function getLimitedData(ServerData\Data $data): string
    {
        return 'mock';
    }
}


class XVolumeModifyPass extends XVolumeModifyFail implements Interfaces\InfoStorage\ForVolume
{}


class XVolumeEncodeFail implements Interfaces\IEncodeSharedKey
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


class XVolumeEncodePass extends XVolumeEncodeFail implements Interfaces\InfoStorage\ForVolume
{}


class XVolumeFormatFail implements Interfaces\IInfoFormatting
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


class XVolumeFormatPass extends XVolumeFormatFail implements Interfaces\InfoStorage\ForVolume
{}
