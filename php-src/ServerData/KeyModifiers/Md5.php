<?php

namespace kalanis\UploadPerPartes\ServerData\KeyModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData\AModifiers;


/**
 * Class Md5
 * @package kalanis\UploadPerPartes\ServerData\KeyModifiers
 */
class Md5 extends AModifiers implements
    Interfaces\IEncodeForExternalExchange,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume,
    Interfaces\IEncodeForInternalStorage
{
    public function getKeyForStorage(string $what): string
    {
        return md5($what);
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
