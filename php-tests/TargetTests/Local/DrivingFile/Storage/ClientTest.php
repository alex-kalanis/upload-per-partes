<?php

namespace TargetTests\Local\DrivingFile\Storage;


use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\UploadException;
use CommonTestClass;


class ClientTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $storage = $this->getLib();
        $this->assertFalse($storage->exists(''));
        $this->assertTrue($storage->exists('testing.upload'));
        $this->assertEquals('abcdefg', $storage->store('testing.upload', 'abcdefg'));
        $this->assertTrue($storage->exists('abcdefg'));
        $this->assertEquals('testing.upload', $storage->get('testing.upload'));
        $this->assertTrue($storage->remove('testing.upload'));
        $this->assertTrue($storage->exists('testing.upload'));
    }

    /**
     * @throws UploadException
     */
    public function testCheckOk(): void
    {
        $lib = $this->getLib();
        $this->assertTrue($lib->checkKeyEncoder(new DrivingFile\KeyEncoders\Serialize()));
    }

    protected function getLib(): DrivingFile\Storage\Client
    {
        return new DrivingFile\Storage\Client();
    }
}
