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
     * @throws UploadException
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        mkdir($file);
        $this->expectExceptionMessage('CANNOT OPEN FILE');
        $this->expectException(UploadException::class);
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz'); // fail
        rmdir($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnreadableSeek(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        mkdir($file);
        $this->expectExceptionMessage('CANNOT OPEN FILE');
        $this->expectException(UploadException::class);
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz', 10); // fail
        rmdir($file);
    }

//    /**
//     * TODO: Someone please describe how to behave when there is unwrittable input
//     * runs with @expectedException
//     * @throws UploadException
//     */
//    public function testUnwriteable(): void
//    {
//        $file = $this->mockTestFile();
//        @rmdir($file);
//        @unlink($file);
//        $storage = $this->mockStorage();
//        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz');
//        chmod($file, 0444);
//        $this->expectException(UploadException::class);
//        $storage->addPart($file, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
//        $this->expectExceptionMessageMatches('CANNOT OPEN FILE');
////        $this->expectExceptionMessageMatches('CANNOT WRITE FILE');
//        chmod($file, 0555);
//    }
//
//    /**
//     * TODO: Someone please describe how to behave when there is unwrittable seeking over input
//     * runs with @expectedException
//     * @throws UploadException
//     */
//    public function testUnwriteableSeek(): void
//    {
//        $file = $this->mockTestFile();
//        @rmdir($file);
//        @unlink($file);
//        $storage = $this->mockStorage();
//        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz', 0);
//        chmod($file, 0444);
//        $this->expectException(UploadException::class);
//        $storage->addPart($file, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 26);
//        $this->expectExceptionMessageMatches('CANNOT OPEN FILE');
////        $this->expectExceptionMessageMatches('CANNOT WRITE FILE');
//        chmod($file, 0555);
//    }

    /**
     * @throws UploadException
     */
    public function testDeleted(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $this->assertFalse($storage->exists($file));
        $storage->addPart($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertTrue($storage->exists($file));
        $storage->remove($file);
        $this->expectExceptionMessage('CANNOT REMOVE DATA');
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
    }

    protected function getTestDir(): string
    {
        return realpath('/tmp/') . '/';
    }
}
