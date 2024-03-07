<?php

namespace kalanis\UploadPerPartes\ServerData\KeyModifiers;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData\AModifiers;


/**
 * Class Hex
 * @package kalanis\UploadPerPartes\ServerData\KeyModifiers
 */
class Hex extends AModifiers implements
    Interfaces\IEncodeSharedKey,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume,
    Interfaces\IStorageKey
{
    public function getKeyForStorage(string $what): string
    {
        return bin2hex($what);
    }

    public function pack(string $data): string
    {
        return bin2hex($data);
    }

    public function unpack(string $data): string
    {
        $pack = @hex2bin($data);
        if (false === $pack) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        return $pack;
    }
}
