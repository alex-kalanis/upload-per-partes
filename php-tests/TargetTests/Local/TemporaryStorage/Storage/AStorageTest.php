<?php

namespace TargetTests\Local\TemporaryStorage\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\UploadException;
use TargetTests\Local\AStorage;


abstract class AStorageTest extends AStorage
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $storage = $this->getLib();
        $this->assertFalse($storage->exists('testing.upload'));
        $this->assertTrue($storage->append('testing.upload', 'abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789'));
        $this->assertTrue($storage->exists('testing.upload'));
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789', $storage->readData('testing.upload', null, null));
        $this->assertEquals('456789abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789', $storage->readData('testing.upload', 30, null));
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz0123', $storage->readData('testing.upload', null, 30));
        $this->assertEquals('456789abcdefghijklmnopqrstuvwx', $storage->readData('testing.upload', 30, 30));
        $this->assertTrue($storage->truncate('testing.upload', 50));
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmn', $storage->readData('testing.upload', null, null));
        $this->assertTrue($storage->append('testing.upload', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'));
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnABCDEFGHIJKLMNOPQRSTUVWXYZ', $storage->readData('testing.upload', null, null));

        $stream = $storage->readStream('testing.upload');
        rewind($stream);
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnABCDEFGHIJKLMNOPQRSTUVWXYZ', stream_get_contents($stream));
        $this->assertTrue($storage->remove('testing.upload'));
    }

    abstract protected function getLib(): Interfaces\ITemporaryStorage;
}
