<?php

namespace ServerDataTests\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\DataModifiers;


class JsonTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new DataModifiers\Json();
        $target = $lib->toFormat($this->mockData());
        $data = $lib->fromFormat($target);

        $this->assertEquals('fghjkl.partial', $data->remoteName);
        $this->assertEquals('/tmp/', $data->tempDir);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
    }
}
