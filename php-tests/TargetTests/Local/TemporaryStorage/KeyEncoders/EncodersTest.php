<?php

namespace TargetTests\Local\TemporaryStorage\KeyEncoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders;
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
        $this->assertEquals($result, $encoder->toPath($this->mockData()));
        $this->assertEquals($result, $encoder->fromData($encoder->toData($this->mockData())));
    }

    public function allProvider(): array
    {
        return [
            [new KeyEncoders\FullPath(), '/abcdefabcdef'],
            [new KeyEncoders\Name(), 'abcdef'],
        ];
    }

    /**
     * @param KeyEncoders\AEncoder $encoder
     * @throws UploadException
     * @dataProvider randomProvider
     */
    public function testRandom(KeyEncoders\AEncoder $encoder): void
    {
        $this->assertNotEmpty($encoder->toPath($this->mockData()));
    }

    public function randomProvider(): array
    {
        return [
            [new KeyEncoders\Random()],
            [new KeyEncoders\SaltedFullPath()],
            [new KeyEncoders\SaltedName()],
        ];
    }
}
