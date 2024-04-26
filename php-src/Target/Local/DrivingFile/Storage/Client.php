<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\Traits\TLangInit;


/**
 * Class Client
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage
 * Storing driving file data at client in system data roundabout
 */
class Client implements Interfaces\IDrivingFile
{
    use TLangInit;

    public function exists(string $key): bool
    {
        return !empty($key);
    }

    public function store(string $key, string $data): string
    {
        return $data;
    }

    public function get(string $key): string
    {
        return $key;
    }

    public function remove(string $key): bool
    {
        return true;
    }

    public function checkKeyEncoder(DrivingFile\KeyEncoders\AEncoder $encoder): bool
    {
        // skip, key is ignored
        return true;
    }
}
