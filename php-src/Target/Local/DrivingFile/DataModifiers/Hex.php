<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers;


use kalanis\UploadPerPartes\UploadException;


/**
 * Class Hex
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers
 */
class Hex extends AModifier
{
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
