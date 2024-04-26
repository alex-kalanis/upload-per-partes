<?php

namespace TargetTests\Local\TemporaryStorage;


use CommonTestClass;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


class TemporaryStorageTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $factory = new Local\TemporaryStorage\Factory();
        $conf = new Config([]);
        $conf->temporaryStorage = new XStorage();
        $conf->temporaryEncoder = new XEncoder();
        $lib = $factory->getTemporaryStorage($conf);

        $this->assertFalse($lib->exists($this->mockData()));
        $lib->read($this->mockData());
        $this->assertEquals('mock', $lib->checksumData($this->mockData(), 999));
        $this->assertTrue($lib->truncate($this->mockData(), 999));
        $this->assertTrue($lib->upload($this->mockData(), 'okmjinuhbzgvtfcrdxesywaq'));
        $this->assertTrue($lib->remove($this->mockData()));
    }

    /**
     * @throws UploadException
     */
    public function testFailedChecksum(): void
    {
        $factory = new Local\TemporaryStorage\Factory();
        $conf = new Config([]);
        $conf->temporaryStorage = new XEmptyDataStorage();
        $conf->temporaryEncoder = new XEncoder();
        $lib = $factory->getTemporaryStorage($conf);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('There is no data for checksum on storage.');
        $lib->checksumData($this->mockData(), 999);
    }
}


class XStorage implements Interfaces\ITemporaryStorage
{
    public function exists(string $path): bool
    {
        return false;
    }

    public function readData(string $path, ?int $fromByte, ?int $length): string
    {
        return 'mock';
    }

    public function truncate(string $path, int $fromByte): bool
    {
        return true;
    }

    public function append(string $path, string $content): bool
    {
        return true;
    }

    public function readStream(string $path)
    {
        return fopen('php://memory', 'rb+');
    }

    public function remove(string $path): bool
    {
        return true;
    }
}


class XEmptyDataStorage extends XStorage
{
    public function readData(string $path, ?int $fromByte, ?int $length): string
    {
        return '';
    }
}


class XEncoder extends Local\TemporaryStorage\KeyEncoders\AEncoder
{
    public function toPath(Data $data): string
    {
        return 'mock';
    }
}
