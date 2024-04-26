<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders;


use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\Uploader\RandomStrings;


/**
 * Class Random
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders
 * Connect shared key and local which has been generated by as random string
 */
class Random extends AEncoder
{
    protected int $keyLength = 64;

    public function toPath(Data $data): string
    {
        return RandomStrings::generate($this->keyLength);
    }
}
