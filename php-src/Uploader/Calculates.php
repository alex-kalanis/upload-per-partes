<?php

namespace kalanis\UploadPerPartes\Uploader;


/**
 * Class Calculates
 * @package kalanis\UploadPerPartes\Uploader
 * Calculations over sizes
 */
class Calculates
{
    protected const DEFAULT_BYTES_PER_PART = 262144; // 1024*256

    /** @var int<0, max> */
    protected int $bytesPerPart = 0;

    public function __construct(Config $config)
    {
        $this->bytesPerPart = empty($config->bytesPerPart)
            ? static::DEFAULT_BYTES_PER_PART
            : max(0, $config->bytesPerPart)
        ;
    }

    /**
     * @return int<0, max>
     */
    public function getBytesPerPart(): int
    {
        return $this->bytesPerPart;
    }

    /**
     * @param int<0, max> $length
     * @return int<0, max>
     */
    public function calcParts(int $length): int
    {
        $partsCounter = abs(intval($length / $this->bytesPerPart));
        return (($length % $this->bytesPerPart) == 0) ? $partsCounter : $partsCounter + 1;
    }

    /**
     * @param Data $data
     * @param int $segment
     * @return int<0, max>
     */
    public function bytesFromSegment(Data $data, int $segment): int
    {
        return max(0, $data->bytesPerPart * $segment);
    }
}
