<?php

namespace TargetTests\Local\FinalStorage\KeyEncoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders;
use kalanis\UploadPerPartes\UploadException;


class EncodersTest extends CommonTestClass
{
    protected function getTestDir(): string
    {
        return '/';
    }

    /**
     * @throws UploadException
     * @param KeyEncoders\AEncoder $encoder
     * @param string $result
     * @dataProvider allProvider
     */
    public function testAll(KeyEncoders\AEncoder $encoder, string $result): void
    {
        $this->assertEquals($result, $encoder->toPath($this->mockData()));
    }

    public function allProvider(): array
    {
        return [
            [new KeyEncoders\FullPath(), '/abcdefabcdef'],
            [new KeyEncoders\Name(), 'abcdef'],
            [new KeyEncoders\TempName(), 'fghjkl.partial'],
        ];
    }

    /**
     * @throws UploadException
     * @param KeyEncoders\AEncoder $encoder
     * @dataProvider randomProvider
     */
    public function testRandom(KeyEncoders\AEncoder $encoder): void
    {
        $this->assertNotEmpty($encoder->toPath($this->mockData()));
    }

    public function randomProvider(): array
    {
        return [
            [new KeyEncoders\SaltedFullPath()],
            [new KeyEncoders\SaltedName()],
        ];
    }
}