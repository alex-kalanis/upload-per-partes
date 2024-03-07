<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData\AModifiers;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\Uploader\RandomStrings;


/**
 * Class SaltedFull
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 */
class SaltedFullPath extends AModifiers implements
    Interfaces\ILimitPassedData,
    Interfaces\InfoStorage\ForKV
{
    public function getLimitedData(Data $data): string
    {
        $want = $data->targetDir . $data->finalName;
        $halfLen = intval(ceil(strlen($want) / 2));
        return RandomStrings::generate($halfLen) . $want . RandomStrings::generate($halfLen);
    }
}
