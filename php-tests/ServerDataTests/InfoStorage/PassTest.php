<?php

namespace ServerDataTests\InfoStorage;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;


class PassTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->getLib();
        $this->assertTrue($storage->save($file, 'abcdefghijklmnopqrstuvwxyz'));
        $this->assertTrue($storage->exists($file));
        $this->assertEquals($this->mockTestFile(), $storage->load($file));
        $this->assertTrue($storage->remove($file));
    }

    /**
     * @throws UploadException
     */
    public function testClassPass(): void
    {
        $pass = new XPassModifyFormatPass();
        $this->assertTrue($this->getLib()->checkKeyClasses(
            $pass,
            new XPassEncodePass(),
            $pass
        ));
    }

    /**
     * @throws UploadException
     */
    public function testClassFailStorageKeyModifier(): void
    {
        $this->expectExceptionMessage('KEY MODIFIER IS WRONG');
        $this->expectException(UploadException::class);
        $this->getLib()->checkKeyClasses(
            new XPassModifyFail(),
            new XPassEncodePass(),
            new XPassModifyFormatPass()
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailStorageKeyVariant(): void
    {
        $pass = new XPassModifyFormatPass();
        $this->expectExceptionMessage('KEY VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $this->getLib()->checkKeyClasses(
            $pass,
            new XPassEncodeFail(),
            $pass
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailDataFormat(): void
    {
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $this->getLib()->checkKeyClasses(
            new XPassModifyFormatPass(),
            new XPassEncodePass(),
            new XPassFormatFail()
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailSharedClass(): void
    {
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $this->getLib()->checkKeyClasses(
            new XPassModifyNotEnough(),
            new XPassEncodePass(),
            new XPassFormatNotEnough()
        );
    }

    protected function getLib(): ServerData\InfoStorage\Pass
    {
        return new ServerData\InfoStorage\Pass();
    }
}


class XPassModifyFail implements Interfaces\ILimitPassedData
{
    public function getLimitedData(ServerData\Data $data): string
    {
        return 'mock';
    }
}


class XPassModifyNotEnough extends XPassModifyFail implements Interfaces\InfoStorage\ForPass
{}


class XPassEncodeFail implements Interfaces\IEncodeSharedKey
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


class XPassEncodePass extends XPassEncodeFail implements Interfaces\InfoStorage\ForPass
{}


class XPassFormatFail implements Interfaces\IInfoFormatting
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


class XPassFormatNotEnough extends XPassFormatFail implements Interfaces\InfoStorage\ForPass
{}


class XPassModifyFormatPass implements
    Interfaces\ILimitPassedData,
    Interfaces\IInfoFormatting,
    Interfaces\InfoStorage\ForPass
{
    public function fromFormat(string $content): ServerData\Data
    {
        return new ServerData\Data();
    }

    public function toFormat(ServerData\Data $data): string
    {
        return 'mock';
    }

    public function getLimitedData(ServerData\Data $data): string
    {
        return 'mock';
    }
}
