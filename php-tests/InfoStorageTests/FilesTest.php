<?php

namespace InfoStorageTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;


class FilesTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoPass();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertTrue($storage->exists($file));
        $storage->load($file);
        $storage->remove($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectException(UploadException::class);
        $storage->load($file);
        $this->expectExceptionMessageMatches('CANNOT READ DRIVEFILE');
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable2(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectException(UploadException::class);
        $storage->load($file);
        $this->expectExceptionMessageMatches('CANNOT READ DRIVEFILE');
    }

    /**
     * @throws UploadException
     */
    public function testUnwrittable(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectException(UploadException::class);
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->expectExceptionMessageMatches('CANNOT WRITE DRIVEFILE');
    }

    /**
     * @throws UploadException
     */
    public function testUnwrittable2(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectException(UploadException::class);
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->expectExceptionMessageMatches('CANNOT WRITE DRIVEFILE');
    }

    /**
     * @throws UploadException
     */
    public function testDeleted(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
        $this->expectExceptionMessageMatches('DRIVEFILE CANNOT BE REMOVED');
    }

    /**
     * @throws UploadException
     */
    public function testDeleted2(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
        $this->expectExceptionMessageMatches('DRIVEFILE CANNOT BE REMOVED');
    }

    /**
     * @throws UploadException
     */
    public function testExistsDied(): void
    {
        $file = $this->mockTestFile();
        $storage = (new \FilesTrait())->mockFilesInfoDie();
        $this->expectException(UploadException::class);
        $storage->exists($file); // dies here
        $this->expectExceptionMessageMatches('CANNOT READ DRIVEFILE');
    }
}
