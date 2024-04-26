<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class AModifier
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers
 * Storing driving file data with at least some encoding of data strings - might not be read as raw data
 * By extending this class and make it use regular cryptography you can practically disable ability to read your
 * datafile by anyone with access to datafile storage.
 */
abstract class AModifier
{
    use TLangInit;

    /**
     * @param string $data
     * @throws UploadException
     * @return string
     */
    abstract public function pack(string $data): string;

    /**
     * @param string $data
     * @throws UploadException
     * @return string
     */
    abstract public function unpack(string $data): string;
}
