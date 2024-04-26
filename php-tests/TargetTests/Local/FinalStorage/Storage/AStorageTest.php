<?php

namespace TargetTests\Local\FinalStorage\Storage;


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

        $stream1 = fopen('php://memory', 'rb+');
        fwrite($stream1, 'testtesttesttest');
        $name1 = $storage->findName('testing.upload');
        $this->assertTrue($storage->store($name1, $stream1));

        $stream2 = fopen('php://memory', 'rb+');
        fwrite($stream2, 'testtesttesttest');
        $name2 = $storage->findName('testing.upload');
        $this->assertTrue($storage->store($name2, $stream2));

        $stream3 = fopen('php://memory', 'rb+');
        fwrite($stream3, 'testtesttesttest');
        $name3 = $storage->findName('testing.upload');
        $this->assertTrue($storage->store($name3, $stream3));
    }

    abstract protected function getLib(): Interfaces\IFinalStorage;
}
