<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class FullPath
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 */
class FullPath extends AEncoder implements
    Interfaces\Storages\ForFiles,
    Interfaces\Storages\ForKV,
    Interfaces\Storages\ForStorage,
    Interfaces\Storages\ForVolume
{
    public function encode(Data $data): string
    {
        return $data->targetDir . $data->targetName;
    }
}
