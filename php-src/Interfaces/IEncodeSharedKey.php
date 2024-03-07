<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Interface IEncodeSharedKey
 * @package kalanis\UploadPerPartes\Interfaces
 * How to pack/unpack key for passing through external part
 */
interface IEncodeSharedKey
{
    public function pack(string $data): string;

    /**
     * @param string $data
     * @throws UploadException
     * @return string
     */
    public function unpack(string $data): string;
}
