<?php

namespace TargetTests\Local\DrivingFile\DataEncoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders;
use kalanis\UploadPerPartes\UploadException;


class EncodersTest extends CommonTestClass
{
    /**
     * @throws UploadException
     * @dataProvider allProvider
     */
    public function testAll(DataEncoders\AEncoder $encoder): void
    {
        $data = $encoder->unpack($encoder->pack($this->mockData()));
        $this->assertEquals('/tmp/', $data->tempDir);
        $this->assertEquals('fghjkl.partial', $data->tempName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->targetDir);
        $this->assertEquals('abcdef', $data->targetName);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
    }

    public function allProvider(): array
    {
        return [
            [new DataEncoders\Json()],
            [new DataEncoders\Line()],
            [new DataEncoders\Serialize()],
            [new DataEncoders\Text()],
        ];
    }

    /**
     * @throws UploadException
     */
    public function testSerializeFail1(): void
    {
        $lib = new DataEncoders\Serialize();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot decode incoming data!');
        $lib->unpack('not serialized string');
    }

    /**
     * @throws UploadException
     */
    public function testSerializeFail2(): void
    {
        $lib = new DataEncoders\Serialize();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot decode incoming data!');
        $lib->unpack('a:2:{i:0;s:10:"serialized";i:1;s:12:"yet no class";}');
    }
}
