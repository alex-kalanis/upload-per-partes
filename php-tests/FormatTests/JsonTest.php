<?php

namespace FormatTests;

use UploadPerPartes\DataFormat;

class JsonTest extends AFormats
{
    public function testTo()
    {
        $lib = new DataFormat\Json();
        $data = $lib->toFormat($this->mockData());

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempPath);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
    }

    public function testThru()
    {
        $lib = new DataFormat\Json();
        $target = $lib->toFormat($this->mockData());
        $data = $lib->fromFormat($target);

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempPath);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
    }
}