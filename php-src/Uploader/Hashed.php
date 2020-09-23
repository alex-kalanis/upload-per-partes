<?php

namespace UploadPerPartes\Uploader;

/**
 * Class Hashed
 * @package UploadPerPartes
 * Calculations hashes, need for checking content
 * Basic one is MD5
 */
class Hashed
{
    public static function init(): Hashed
    {
        return new static();
    }

    public function calcHash(string $content): string
    {
        return md5($content);
    }
}