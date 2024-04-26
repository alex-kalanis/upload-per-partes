<?php

namespace TargetTests\Local\DrivingFile\Storage;


use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Processing;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Storage;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
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

    /**
     * @throws FilesException
     * @throws PathsException
     * @return Interfaces\IDrivingFile
     */
    protected function getLib(): Interfaces\IDrivingFile
    {
        return new DrivingFile\Storage\Files(
            (new Access\Factory())->getClass(new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()))
        );
    }

    protected function getOkEncoder(): DrivingFile\KeyEncoders\AEncoder
    {
        return new DrivingFile\KeyEncoders\Name();
    }

    protected function getFailEncoder(): DrivingFile\KeyEncoders\AEncoder
    {
        return new DrivingFile\KeyEncoders\SaltedName();
    }

    protected function getFailedLib(): Interfaces\IDrivingFile
    {
        $storage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());
        return new DrivingFile\Storage\Files(new Access\CompositeAdapter(
            new XFailNode($storage),
            new Processing\Storage\ProcessDir($storage),
            new XFailFile($storage),
            new XFailStream($storage)
        ));
    }

    protected function getFailedWriteLib(): Interfaces\IDrivingFile
    {
        $storage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());
        return new DrivingFile\Storage\Files(new Access\CompositeAdapter(
            new Processing\Storage\ProcessNode($storage),
            new Processing\Storage\ProcessDir($storage),
            new XFailWriteFile($storage),
            new Processing\Storage\ProcessFileStream($storage)
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


class XFailWriteFile extends Processing\Storage\ProcessFile
{
    public function saveFile(array $entry, string $content, ?int $offset = null, int $mode = 0): bool
    {
        return false;
    }
}


class XFailStream extends Processing\Storage\ProcessFileStream
{
    public function saveFileStream(array $entry, $content, int $mode = 0, bool $throwErrorForParent = true): bool
    {
        throw new FilesException('mock');
    }
}
