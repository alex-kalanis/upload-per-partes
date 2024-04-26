<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers;


/**
 * Class Clear
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers
 */
class Clear extends AModifier
{
    public function pack(string $data): string
    {
        return $data;
    }

    public function unpack(string $data): string
    {
        return $data;
    }
}
