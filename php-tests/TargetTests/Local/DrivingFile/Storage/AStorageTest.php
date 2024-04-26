<?php

namespace TargetTests\Local\DrivingFile\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
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
        $this->assertEquals('testing.upload', $storage->store('testing.upload', 'abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789'));
        $this->assertTrue($storage->exists('testing.upload'));
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789', $storage->get('testing.upload'));
        $this->assertTrue($storage->remove('testing.upload'));
        $this->assertFalse($storage->exists('testing.upload'));
    }

    /**
     * @throws UploadException
     */
    public function testOkEncoder(): void
    {
        $storage = $this->getLib();
        $this->assertTrue($storage->checkKeyEncoder($this->getOkEncoder()));
    }

    /**
     * @throws UploadException
     */
    public function testFailEncoder(): void
    {
        $storage = $this->getLib();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving data key encoder variant is set in a wrong way. Cannot determine it.');
        $storage->checkKeyEncoder($this->getFailEncoder());
    }

    abstract protected function getLib(): Interfaces\IDrivingFile;

    abstract protected function getOkEncoder(): DrivingFile\KeyEncoders\AEncoder;

    abstract protected function getFailEncoder(): DrivingFile\KeyEncoders\AEncoder;
}
