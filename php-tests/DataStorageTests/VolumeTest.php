<?php

namespace DataStorageTests;

use UploadPerPartes\Exceptions\UploadException;

class VolumeTest extends AStorage
{
    /**
     * @throws UploadException
     */
    public function testThru()
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
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT OPEN FILE
     */
    public function testUnreadable()
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        mkdir($file);
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz'); // fail
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT OPEN FILE
     */
    public function testUnreadableSeek()
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        mkdir($file);
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz', 10); // fail
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT OPEN FILE
     * @-expectedExceptionMessage CANNOT WRITE FILE
     */
    public function testUnwriteable()
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz');
        chmod($file, 0444);
        $storage->addPart($file, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT OPEN FILE
     * @-expectedExceptionMessage CANNOT WRITE FILE
     */
    public function testUnwriteableSeek()
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz', 0);
        chmod($file, 0444);
        $storage->addPart($file, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 26);
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT REMOVE DATA
     */
    public function testDeleted()
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