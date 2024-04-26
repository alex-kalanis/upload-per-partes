<?php

namespace TargetTests\Local\DrivingFile\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\UploadException;


class VolumeTest extends AStorageTest
{
    /**
     * @throws UploadException
     */
    public function testReadFail(): void
    {
        $storage = $this->getLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot read *this-file-does-not-exists* driving file from its storage.');
        $storage->get('this-file-does-not-exists');
    }

    /**
     * @throws UploadException
     */
    public function testWriteFail(): void
    {
        $storage = $this->getLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot write *//localhost/tmp/foo/this-file-does-not-exists* driving file into its storage.');
        $storage->store('//localhost/tmp/foo/this-file-does-not-exists', 'blablablabla');
    }

    protected function getOkEncoder(): DrivingFile\KeyEncoders\AEncoder
    {
        return new DrivingFile\KeyEncoders\Name();
    }

    protected function getFailEncoder(): DrivingFile\KeyEncoders\AEncoder
    {
        return new DrivingFile\KeyEncoders\SaltedName();
    }

    protected function getLib(): Interfaces\IDrivingFile
    {
        return new DrivingFile\Storage\Volume($this->getTestDir());
    }
}
