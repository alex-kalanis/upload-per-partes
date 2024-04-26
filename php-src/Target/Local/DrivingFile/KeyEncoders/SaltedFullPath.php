<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\Uploader\RandomStrings;


/**
 * Class SaltedFull
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders
 */
class SaltedFullPath extends AEncoder implements
    Interfaces\Storages\ForKV
{
    public function encode(Data $data): string
    {
        $want = $data->targetDir . $data->targetName;
        $halfLen = intval(ceil(strlen($want) / 2));
        return RandomStrings::generate($halfLen) . $want . RandomStrings::generate($halfLen);
    }
}
