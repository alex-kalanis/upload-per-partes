<?php

namespace TargetTests\Local\TemporaryStorage\Storage;


use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\TemporaryStorage;
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
    public function testReadFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $lib->readData('something', 0, 9999);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $lib->truncate('something', 20);
    }

    /**
     * @throws UploadException
     */
    public function testWriteFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $lib->append('something', 'testtesttesttest');
    }

    /**
     * @throws UploadException
     */
    public function testStreamFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $lib->readStream('something');
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

    protected function getLib(): Interfaces\ITemporaryStorage
    {
        return new TemporaryStorage\Storage\Storage(new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()));
    }

    protected function getFailedLib(): Interfaces\ITemporaryStorage
    {
        return new TemporaryStorage\Storage\Storage(new XFailStorage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()));
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
