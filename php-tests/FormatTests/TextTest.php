<?php

namespace FormatTests;

use UploadPerPartes\DataFormat;

class TextTest extends AFormats
{
    public function testThru()
    {
        $lib = new DataFormat\Text();
        $target = $lib->toFormat($this->mockData());
        $data = $lib->fromFormat($target);

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempLocation);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
    }
}