<?php

namespace TargetTests\Local\FinalStorage\Storage;


use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Processing;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Storage;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\FinalStorage;
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
    public function testWriteFail(): void
    {
        $lib = $this->getFailedLib();
        $stream = fopen('php://memory', 'rb+');
        fwrite($stream, 'testtesttesttest');

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot write file *something*');
        $lib->store('something', $stream);
    }

    /**
     * @throws UploadException
     */
    public function testLookupFail(): void
    {
        $lib = $this->getFailedLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot write file *something*');
        $lib->findName('something');
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return Interfaces\IFinalStorage
     */
    protected function getLib(): Interfaces\IFinalStorage
    {
        return new FinalStorage\Storage\Files(
            (new Access\Factory())->getClass(new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()))
        );
    }

    protected function getFailedLib(): Interfaces\IFinalStorage
    {
        $storage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());
        return new FinalStorage\Storage\Files(new Access\CompositeAdapter(
            new XFailNode($storage),
            new Processing\Storage\ProcessDir($storage),
            new Processing\Storage\ProcessFile($storage),
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


class XFailStream extends Processing\Storage\ProcessFileStream
{
    public function saveFileStream(array $entry, $content, int $mode = 0, bool $throwErrorForParent = true): bool
    {
        throw new FilesException('mock');
    }
}
