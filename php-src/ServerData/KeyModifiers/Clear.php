<?php

namespace kalanis\UploadPerPartes\ServerData\KeyModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData\AModifiers;


/**
 * Class Clear
 * @package kalanis\UploadPerPartes\ServerData\KeyModifiers
 */
class Clear extends AModifiers implements
    Interfaces\IEncodeSharedKey,
    Interfaces\IStorageKey,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForPass
{
    public function getKeyForStorage(string $what): string
    {
        return $what;
    }

    public function pack(string $data): string
    {
        return $data;
    }

    public function unpack(string $data): string
    {
        return $data;
    }
}
