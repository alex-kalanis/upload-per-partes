<?php

namespace InfoStorageTests;

use UploadPerPartes\Exceptions\UploadException;

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
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT READ DRIVEFILE
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $storage->load($file);
        chmod($file, 0333);
        $storage->load($file);
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage CANNOT WRITE DRIVEFILE
     */
    public function testUnwriteable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        chmod($file, 0444);
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage DRIVEFILE CANNOT BE REMOVED
     */
    public function testDeleted(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $storage->remove($file);
        $storage->remove($file); // dies here
    }
}