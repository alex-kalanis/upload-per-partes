<?php

namespace kalanis\UploadPerPartes\Uploader;


/**
 * Class CheckByHash
 * @package kalanis\UploadPerPartes\Uploader
 * Calculations hashes, need for checking content
 * Basic one is MD5
 */
class CheckByHash
{
    public function calcHash(string $content): string
    {
        return md5($content);
    }
}
