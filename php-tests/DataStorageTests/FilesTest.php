<?php

namespace DataStorageTests;


use kalanis\UploadPerPartes\Exceptions\UploadException;


class FilesTest extends AStorage
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesDataPass();
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz', $storage->getPart($file, 0));
        $storage->truncate($file, 16);
        $this->assertEquals('abcdefghijklmnop', $storage->getPart($file, 0));
        $storage->remove($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesDataDie();
        $this->expectException(UploadException::class);
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz'); // fail
    }

    /**
     * @throws UploadException
     */
    public function testUnreadableSeek(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesDataDie();
        $this->expectException(UploadException::class);
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz', 10); // fail
    }

    /**
     * @throws UploadException
     */
    public function testExistsFail(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesDataDie();
        $this->expectException(UploadException::class);
        $storage->exists($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnreachable(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesDataDie();
        $this->expectException(UploadException::class);
        $storage->getPart($file, 10); // fail
    }

    /**
     * @throws UploadException
     */
    public function testUncut(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesDataDie();
        $this->expectException(UploadException::class);
        $storage->truncate($file, 10); // fail
    }

    /**
     * @throws UploadException
     */
    public function testDeleted(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesDataPass();
        $this->assertTrue($storage->exists($file)); // because mocked
        $storage->remove($file);
    }

    /**
     * @throws UploadException
     */
    public function testDeletedDie(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesDataDie();
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
    }

    protected function getTestDir(): string
    {
        return '/tmp/';
    }
}
