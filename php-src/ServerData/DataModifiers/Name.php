<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData\AModifiers;
use kalanis\UploadPerPartes\ServerData\Data;


/**
 * Class Name
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 */
class Name extends AModifiers implements
    Interfaces\ILimitDataInternalKey,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume
{
    public function getLimitedData(Data $data): string
    {
        return $data->remoteName;
    }
}
