<?php

namespace ServerDataTests\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\DataModifiers;


class SerializeTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $lib = new DataModifiers\Serialize();
        $data = $lib->fromFormat($lib->toFormat($this->mockData()));

        $this->assertEquals('fghjkl.partial', $data->remoteName);
        $this->assertEquals('/tmp/', $data->tempDir);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
    }

    /**
     * @throws UploadException
     */
    public function testDeserializeFail(): void
    {
        $lib = new DataModifiers\Serialize();
        $this->expectExceptionMessage('CANNOT DECODE INCOMING DATA');
        $this->expectException(UploadException::class);
        $lib->fromFormat('--this-is-not-a-serialized-string--');
    }

    /**
     * @throws UploadException
     */
    public function testDeserializeSomethingElse(): void
    {
        $lib = new DataModifiers\Serialize();
        $this->expectExceptionMessage('CANNOT DECODE INCOMING DATA');
        $this->expectException(UploadException::class);
        $lib->fromFormat('a:2:{i:0;s:3:"foo";i:1;s:3:"bar";}');
    }
}
