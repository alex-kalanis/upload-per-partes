<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData\AModifiers;
use kalanis\UploadPerPartes\ServerData\Data;


/**
 * Class FullPath
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 */
class FullPath extends AModifiers implements
    Interfaces\ILimitPassedData,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume
{
    public function getLimitedData(Data $data): string
    {
        return $data->targetDir . $data->finalName;
    }
}
