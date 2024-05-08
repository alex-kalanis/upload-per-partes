<?php

namespace TargetTests\Local\DrivingFile\KeyEncoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders;
use kalanis\UploadPerPartes\UploadException;


class EncodersTest extends CommonTestClass
{
    protected function getTestDir(): string
    {
        return '/';
    }

    /**
     * @param KeyEncoders\AEncoder $encoder
     * @param string $result
     * @throws UploadException
     * @dataProvider allProvider
     */
    public function testAll(KeyEncoders\AEncoder $encoder, string $result): void
    {
        $this->assertEquals($result, $encoder->encode($this->mockData()));
    }

    public function allProvider(): array
    {
        return [
            [new KeyEncoders\FullPath(), '/abcdefabcdef'],
            [new KeyEncoders\Json(), '{"tempDir":"\/tmp\/","tempName":"fghjkl.partial","targetDir":"\/abcdef","targetName":"abcdef","fileSize":123456,"partsCount":12,"bytesPerPart":64,"lastKnownPart":7}'],
            [new KeyEncoders\Name(), 'abcdef'],
            [new KeyEncoders\Serialize(), 'O:37:"kalanis\UploadPerPartes\Uploader\Data":8:{s:7:"tempDir";s:5:"/tmp/";s:8:"tempName";s:14:"fghjkl.partial";s:9:"targetDir";s:7:"/abcdef";s:10:"targetName";s:6:"abcdef";s:8:"fileSize";i:123456;s:10:"partsCount";i:12;s:12:"bytesPerPart";i:64;s:13:"lastKnownPart";i:7;}'],
        ];
    }

    /**
     * @param KeyEncoders\AEncoder $encoder
     * @throws UploadException
     * @dataProvider randomProvider
     */
    public function testRandom(KeyEncoders\AEncoder $encoder): void
    {
        $this->assertNotEmpty($encoder->encode($this->mockData()));
    }

    public function randomProvider(): array
    {
        return [
            [new KeyEncoders\SaltedFullPath()],
            [new KeyEncoders\SaltedName()],
        ];
    }
}
