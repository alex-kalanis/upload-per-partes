<?php

namespace kalanis\UploadPerPartes\Uploader;


/**
 * Class Hashed
 * @package kalanis\UploadPerPartes
 * Calculations hashes, need for checking content
 * Basic one is MD5
 */
class Hashed
{
    public function calcHash(string $content): string
    {
        return md5($content);
    }
}
