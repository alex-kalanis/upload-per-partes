<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers;


/**
 * Class Suffix
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyModifiers
 */
class Suffix extends AModifier
{
    protected string $suffix = '.upload';

    public function pack(string $data): string
    {
        return $data . $this->suffix;
    }

    public function unpack(string $data): string
    {
        $len = strlen($this->suffix);
        $start = substr($data, 0, -1 * $len);
        $end = substr($data, -1 * $len);
        return $start . strtr($end, [$this->suffix => '']);
    }
}
