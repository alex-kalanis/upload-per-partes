<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData\AModifiers;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\Uploader\RandomStrings;


/**
 * Class SaltedName
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 */
class SaltedName extends AModifiers implements
    Interfaces\ILimitDataInternalKey,
    Interfaces\InfoStorage\ForVolume,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForFiles
{
    public function getLimitedData(Data $data): string
    {
        $want = $data->remoteName;
        $halfLen = intval(ceil(strlen($want) / 2));
        return RandomStrings::generate($halfLen) . $want . RandomStrings::generate($halfLen);
    }
}
