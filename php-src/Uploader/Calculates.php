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

    public static function init(int $bytesPerPart = null): Calculates
    {
        return new static($bytesPerPart);
    }

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
        $partsCounter = (int)($length / $this->bytesPerPart);
        return (($length % $this->bytesPerPart) == 0) ? (int)$partsCounter : (int)($partsCounter + 1);
    }
}
