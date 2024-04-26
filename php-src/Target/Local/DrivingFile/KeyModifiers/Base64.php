<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers;


use kalanis\UploadPerPartes\UploadException;


/**
 * Class Base64
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers
 */
class Base64 extends AModifier
{
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
