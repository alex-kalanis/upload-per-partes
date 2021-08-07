<?php

namespace InfoStorageTests;


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
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertTrue($storage->exists($file));
        $storage->load($file);
        $storage->remove($file);
        $this->assertFalse($storage->exists($file));
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $storage->load($file);
        chmod($file, 0333);
        $this->expectException(UploadException::class);
        $storage->load($file);
        $this->expectExceptionMessageMatches('CANNOT READ DRIVEFILE');
    }

    /**
     * @throws UploadException
     */
    public function testUnwriteable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        chmod($file, 0444);
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
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $storage->remove($file);
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
        $this->expectExceptionMessageMatches('DRIVEFILE CANNOT BE REMOVED');
    }
}
