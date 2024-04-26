<?php

namespace TargetTests\Local\TemporaryStorage\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\TemporaryStorage;
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
        $this->expectExceptionMessage('Cannot read file *this-file-does-not-exists*');
        $storage->readData('this-file-does-not-exists', null, null);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateFail(): void
    {
        $storage = $this->getLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot write file *this-file-does-not-exists*');
        $storage->truncate('this-file-does-not-exists', 60);
    }

    /**
     * @throws UploadException
     */
    public function testStreamFail(): void
    {
        $storage = $this->getLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot read file *this-file-does-not-exists*');
        $storage->readStream('this-file-does-not-exists');
    }

    protected function getLib(): Interfaces\ITemporaryStorage
    {
        return new TemporaryStorage\Storage\Volume($this->getTestDir());
    }
}
