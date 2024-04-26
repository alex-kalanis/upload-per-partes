<?php

namespace kalanis\UploadPerPartes\Target\Local\Checksums;


use kalanis\UploadPerPartes\Interfaces\IChecksum;


/**
 * Class Md5
 * @package kalanis\UploadPerPartes\Target\Local\Checksums
 * Calculate checksum for passed part
 */
class Md5 implements IChecksum
{
    public function getMethod(): string
    {
        return 'md5';
    }

    public function checksum(string $data): string
    {
        return md5($data);
    }
}
