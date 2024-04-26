<?php

namespace TargetTests\Local\DrivingFile\Storage;


use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\UploadException;


class StorageTest extends AStorageTest
{
    /**
     * @throws UploadException
     */
    public function testExistsFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $lib->exists('something');
    }

    /**
     * @throws UploadException
     */
    public function testStoreFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $lib->store('something', 'blablablabla');
    }

    /**
     * @throws UploadException
     */
    public function testWriteFail(): void
    {
        $lib = $this->getFailedWriteLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot write *something* driving file into its storage.');
        $lib->store('something', 'blablablabla');
    }

    /**
     * @throws UploadException
     */
    public function testGetFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $lib->get('something');
    }

    /**
     * @throws UploadException
     */
    public function testRemoveFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $lib->remove('something');
    }

    protected function getLib(): Interfaces\IDrivingFile
    {
        return new DrivingFile\Storage\Storage(new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()));
    }

    protected function getOkEncoder(): DrivingFile\KeyEncoders\AEncoder
    {
        return new DrivingFile\KeyEncoders\Name();
    }

    protected function getFailEncoder(): DrivingFile\KeyEncoders\AEncoder
    {
        return new DrivingFile\KeyEncoders\SaltedName();
    }

    protected function getFailedWriteLib(): Interfaces\IDrivingFile
    {
        return new DrivingFile\Storage\Storage(new XWriteStorage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()));
    }

    protected function getFailedLib(): Interfaces\IDrivingFile
    {
        return new DrivingFile\Storage\Storage(new XFailStorage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()));
    }
}


class XWriteStorage extends Storage\Storage
{
    public function write(string $sharedKey, string $data, ?int $timeout = null): bool
    {
        return false;
    }
}


class XFailStorage extends Storage\Storage
{
    public function exists(string $sharedKey): bool
    {
        throw new StorageException('mock');
    }

    public function read(string $sharedKey): string
    {
        throw new StorageException('mock');
    }

    public function write(string $sharedKey, string $data, ?int $timeout = null): bool
    {
        throw new StorageException('mock');
    }

    public function remove(string $sharedKey): bool
    {
        throw new StorageException('mock');
    }
}
