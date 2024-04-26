<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class Serialize
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders
 * Driver file - format into serialized string
 */
class Serialize extends AEncoder implements
    Interfaces\Storages\ForClient,
    Interfaces\Storages\ForFiles,
    Interfaces\Storages\ForKV,
    Interfaces\Storages\ForStorage,
    Interfaces\Storages\ForVolume
{
    public function encode(Data $data): string
    {
        return strval(serialize($data));
    }
}
