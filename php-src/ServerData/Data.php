<?php

namespace kalanis\UploadPerPartes\ServerData;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;


/**
 * Class Data
 * @package kalanis\UploadPerPartes\ServerData
 * Shared metadata passing there and back about upload itself
 */
final class Data implements IDriverLocation
{
    /** @var string what path/prefix on storage to driver file */
    public $pathPrefix = '';
    /** @var string real driver file name on storage */
    public $sharedKey = '';

    public function getDriverPrefix(): string
    {
        return strval($this->pathPrefix);
    }

    public function getDriverKey(): string
    {
        return strval($this->sharedKey);
    }
}
