<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class AEncoder
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders
 * How to pack/unpack driving data in storage
 */
abstract class AEncoder
{
    use TLangInit;

    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    abstract public function pack(Data $data): string;

    /**
     * @param string $data
     * @throws UploadException
     * @return Data
     */
    abstract public function unpack(string $data): Data;
}
