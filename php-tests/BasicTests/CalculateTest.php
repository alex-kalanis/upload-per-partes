<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Uploader;


class CalculateTest extends CommonTestClass
{
    public function testBytes1(): void
    {
        $conf = new Uploader\Config([]);
        $lib = new Uploader\Calculates($conf);
        $this->assertEquals(262144, $lib->getBytesPerPart());
    }

    /**
     * @param int $value
     * @param int $result
     * @dataProvider bytesProvider
     */
    public function testBytes2(int $value, int $result): void
    {
        $conf = new Uploader\Config([]);
        $conf->bytesPerPart = $value;
        $lib = new Uploader\Calculates($conf);
        $this->assertEquals($result, $lib->getBytesPerPart());
    }

    public function bytesProvider(): array
    {
        return [
            [-22, 0],
            [834158, 834158],
        ];
    }

    /**
     * @param int $perPart
     * @param int $totalLength
     * @param int $result
     * @dataProvider partsProvider
     */
    public function testParts(int $perPart, int $totalLength, int $result): void
    {
        $config = new Uploader\Config([]);
        $config->bytesPerPart = $perPart;
        $lib = new Uploader\Calculates($config);
        $this->assertEquals($result, $lib->calcParts($totalLength));
    }

    public function partsProvider(): array
    {
        return [
            [1, 1, 1],
            [1, 7, 7],
            [10, 8, 1],
            [10, 11, 2],

            [20, 35, 2],
            [20, 40, 2],
            [20, 41, 3],
        ];
    }

    /**
     * @param int $bytes
     * @param int $segment
     * @param int $result
     * @dataProvider segmentProvider
     */
    public function testSegment(int $bytes, int $segment, int $result): void
    {
        $data = new Uploader\Data();
        $data->bytesPerPart = $bytes;
        $lib = new Uploader\Calculates(new Uploader\Config([]));
        $this->assertEquals($result, $lib->bytesFromSegment($data, $segment));
    }

    public function segmentProvider(): array
    {
        return [
            [1, 1, 1],
            [100, 100, 10000],
        ];
    }
}
