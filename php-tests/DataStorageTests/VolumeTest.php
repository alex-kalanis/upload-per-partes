<?php

namespace DataStorageTests;


use kalanis\UploadPerPartes\Exceptions\UploadException;


class VolumeTest extends AStorage
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz', $storage->getPart($file, 0));
        $storage->truncate($file, 16);
        $this->assertEquals('abcdefghijklmnop', $storage->getPart($file, 0));
        $storage->remove($file);
        $this->assertFalse(is_file($file));
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT OPEN FILE
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        mkdir($file);
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz'); // fail
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT OPEN FILE
     */
    public function testUnreadableSeek(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        mkdir($file);
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz', 10); // fail
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT OPEN FILE
     * @-expectedExceptionMessage CANNOT WRITE FILE
     */
    public function testUnwriteable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz');
        chmod($file, 0444);
        $storage->addPart($file, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT OPEN FILE
     * @-expectedExceptionMessage CANNOT WRITE FILE
     */
    public function testUnwriteableSeek(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz', 0);
        chmod($file, 0444);
        $storage->addPart($file, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 26);
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT REMOVE DATA
     */
    public function testDeleted(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz');
        $storage->remove($file);
        $storage->remove($file); // dies here
    }

    protected function getTestDir(): string
    {
        return realpath('/tmp/') . '/';
    }
}