<?php

namespace kalanis\UploadPerPartes\Uploader;


/**
 * Class Calculates
 * @package kalanis\UploadPerPartes
 * Calculations over sizes
 */
class Calculates
{
    const DEFAULT_BYTES_PER_PART = 262144; // 1024*256

    /** @var int<0, max> */
    protected $bytesPerPart = 0;

    /**
     * @param int<0, max>|null $bytesPerPart
     */
    public function __construct(int $bytesPerPart = null)
    {
        $this->bytesPerPart = empty($bytesPerPart) ? static::DEFAULT_BYTES_PER_PART : $bytesPerPart ;
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
}
