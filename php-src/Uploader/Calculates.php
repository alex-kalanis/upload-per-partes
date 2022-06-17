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

    /** @var int */
    protected $bytesPerPart = 0;

    public function __construct(int $bytesPerPart = null)
    {
        $this->bytesPerPart = empty($bytesPerPart) ? static::DEFAULT_BYTES_PER_PART : $bytesPerPart ;
    }

    public function getBytesPerPart(): int
    {
        return $this->bytesPerPart;
    }

    public function calcParts(int $length): int
    {
        $partsCounter = intval($length / $this->bytesPerPart);
        return (($length % $this->bytesPerPart) == 0) ? intval($partsCounter) : intval($partsCounter + 1);
    }
}
