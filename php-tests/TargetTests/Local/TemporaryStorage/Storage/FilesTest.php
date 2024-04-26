<?php

namespace TargetTests\Local\TemporaryStorage\Storage;


use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Processing;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Storage;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\TemporaryStorage;
use kalanis\UploadPerPartes\UploadException;


class FilesTest extends AStorageTest
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
        $this->expectExceptionMessage('Cannot copy file to destination');
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
        $this->expectExceptionMessage('Cannot load wanted file.');
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

    /**
     * @throws FilesException
     * @throws PathsException
     * @return Interfaces\ITemporaryStorage
     */
    protected function getLib(): Interfaces\ITemporaryStorage
    {
        return new TemporaryStorage\Storage\Files(
            (new Access\Factory())->getClass(new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()))
        );
    }

    protected function getFailedLib(): Interfaces\ITemporaryStorage
    {
        $storage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());
        return new TemporaryStorage\Storage\Files(new Access\CompositeAdapter(
            new XFailNode($storage),
            new Processing\Storage\ProcessDir($storage),
            new XFailFile($storage),
            new XFailStream($storage)
        ));
    }
}


class XFailNode extends Processing\Storage\ProcessNode
{
    public function exists(array $entry): bool
    {
        throw new FilesException('mock');
    }
}


class XFailFile extends Processing\Storage\ProcessFile
{
    public function readFile(array $entry, ?int $offset = null, ?int $length = null): string
    {
        throw new FilesException('mock');
    }

    public function saveFile(array $entry, string $content, ?int $offset = null, int $mode = 0): bool
    {
        throw new FilesException('mock');
    }

    public function deleteFile(array $entry): bool
    {
        throw new FilesException('mock');
    }
}


class XFailStream extends Processing\Storage\ProcessFileStream
{
    public function saveFileStream(array $entry, $content, int $mode = 0, bool $throwErrorForParent = true): bool
    {
        throw new FilesException('mock');
    }
}
