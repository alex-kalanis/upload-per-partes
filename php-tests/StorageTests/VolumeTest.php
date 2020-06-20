<?php

namespace StorageTests;

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
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertTrue($storage->exists($file));
        $storage->load($file);
        $storage->remove($file);
        $this->assertFalse($storage->exists($file));
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     */
    public function testUnreadable()
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
     */
    public function testUnwriteable()
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        chmod($file, 0444);
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     */
    public function testDeleted()
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $storage->remove($file);
        $storage->remove($file); // dies here
    }
}