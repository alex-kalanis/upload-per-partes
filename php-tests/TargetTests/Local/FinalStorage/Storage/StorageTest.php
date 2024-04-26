<?php

namespace TargetTests\Local\FinalStorage\Storage;


use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\FinalStorage;
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
    public function testWriteFail(): void
    {
        $lib = $this->getFailedLib();
        $stream = fopen('php://memory', 'rb+');
        fwrite($stream, 'testtesttesttest');

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot write file *something*');
        $lib->store('something', $stream);
    }

    protected function getLib(): Interfaces\IFinalStorage
    {
        return new FinalStorage\Storage\Storage(new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()));
    }

    protected function getFailedLib(): Interfaces\IFinalStorage
    {
        return new FinalStorage\Storage\Storage(new XFailStorage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()));
    }
}


class XFailStorage extends Storage\Storage
{
    public function exists(string $sharedKey): bool
    {
        throw new StorageException('mock');
    }

    public function write(string $sharedKey, string $data, ?int $timeout = null): bool
    {
        throw new StorageException('mock');
    }
}
