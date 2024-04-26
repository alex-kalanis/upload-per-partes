<?php

namespace kalanis\UploadPerPartes\Target\Local\Checksums;


use kalanis\UploadPerPartes\Interfaces\IChecksum;


/**
 * Class Sha1
 * @package kalanis\UploadPerPartes\Target\Local\Checksums
 * Calculate checksum for passed part
 */
class Sha1 implements IChecksum
{
    public function getMethod(): string
    {
        return 'sha1';
    }

    public function checksum(string $data): string
    {
        return sha1($data);
    }
}
