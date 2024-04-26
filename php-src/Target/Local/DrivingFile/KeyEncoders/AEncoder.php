<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class AEncoder
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders
 * Encode user data to shared key for usage by both server and client
 */
abstract class AEncoder
{
    use TLangInit;

    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    abstract public function encode(Data $data): string;
}
