<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Uploader;


class DataPackTest extends CommonTestClass
{
    public function testCreate1(): void
    {
        $lib = new Uploader\DataPack(new Uploader\Data());
        $data = $lib->create('foo', 'bar', 123456);
        $this->assertEquals('foo', $data->targetDir);
        $this->assertEquals('bar', $data->targetName);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals('bar', $lib->getFinalName($data));
    }

    public function testCreate2(): void
    {
        $lib = new Uploader\DataPack(new Uploader\Data());
        $data = $lib->create('foo', 'bar', -123);
        $this->assertEquals('foo', $data->targetDir);
        $this->assertEquals('bar', $data->targetName);
        $this->assertEquals(0, $data->fileSize);
        $this->assertEquals('bar', $lib->getFinalName($data));
    }

    public function testSizes1(): void
    {
        $lib = new Uploader\DataPack(new Uploader\Data());
        $data = $lib->fillSizes(new Uploader\Data(), 123, 456, 789);
        $this->assertEquals(123, $data->partsCount);
        $this->assertEquals(456, $data->bytesPerPart);
        $this->assertEquals(789, $data->lastKnownPart);
    }

    public function testSizes2(): void
    {
        $lib = new Uploader\DataPack(new Uploader\Data());
        $data = $lib->fillSizes(new Uploader\Data(), -123, -456, -789);
        $this->assertEquals(0, $data->partsCount);
        $this->assertEquals(0, $data->bytesPerPart);
        $this->assertEquals(0, $data->lastKnownPart);
    }

    public function testTempDir(): void
    {
        $lib = new Uploader\DataPack(new Uploader\Data());
        $data = $lib->fillTempData(new Uploader\Data(), new Uploader\Config(['temp_location' => 'whatever']));
        $this->assertEquals('whatever', $data->tempDir);
    }

    public function testNextSegment1(): void
    {
        $lib = new Uploader\DataPack(new Uploader\Data());
        $cfg = new Uploader\Data();
        $this->assertEquals(1, $lib->nextSegment($cfg));
    }

    public function testNextSegment2(): void
    {
        $cfg = new Uploader\Data();
        $cfg->lastKnownPart = 357;
        $lib = new Uploader\DataPack(new Uploader\Data());
        $this->assertEquals(358, $lib->nextSegment($cfg));
        $this->assertEquals(255, $lib->nextSegment($lib->lastKnown($cfg, 254)));
    }
}
