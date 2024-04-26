<?php

namespace TargetTests\Local\FinalStorage;


use CommonTestClass;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


class FinalStorageTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $factory = new Local\FinalStorage\Factory();
        $conf = new Config([]);
        $conf->finalStorage = new XStorage();
        $conf->finalEncoder = new XEncoder();
        $lib = $factory->getFinalStorage($conf);

        $name = $lib->findName($this->mockData());
        $this->assertEquals('mock', $name);
        $this->assertFalse($lib->exists($this->mockData()));
        $this->assertTrue($lib->store($name, fopen('php://memory', 'rb+')));
    }
}


class XStorage implements Interfaces\IFinalStorage
{
    public function exists(string $path): bool
    {
        return false;
    }

    public function store(string $path, $source): bool
    {
        return true;
    }

    public function findName(string $key): string
    {
        return $key;
    }
}


class XEncoder extends Local\FinalStorage\KeyEncoders\AEncoder
{
    public function toPath(Data $data): string
    {
        return 'mock';
    }
}
