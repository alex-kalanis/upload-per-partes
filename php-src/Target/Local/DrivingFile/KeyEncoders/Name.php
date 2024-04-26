<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class Name
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders
 */
class Name extends AEncoder implements
    Interfaces\Storages\ForFiles,
    Interfaces\Storages\ForKV,
    Interfaces\Storages\ForStorage,
    Interfaces\Storages\ForVolume
{
    public function encode(Data $data): string
    {
        return $data->targetName;
    }
}
