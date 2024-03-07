<?php

namespace kalanis\UploadPerPartes\ServerData\KeyModifiers;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData\AModifiers;


/**
 * Class Base64
 * @package kalanis\UploadPerPartes\ServerData\KeyModifiers
 */
class Base64 extends AModifiers implements
    Interfaces\IEncodeSharedKey,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume,
    Interfaces\IStorageKey
{
    public function getKeyForStorage(string $what): string
    {
        return base64_encode($what);
    }

    public function pack(string $data): string
    {
        return base64_encode($data);
    }

    public function unpack(string $data): string
    {
        $pack = @base64_decode($data, true);
        if (false === $pack) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        return $pack;
    }
}
