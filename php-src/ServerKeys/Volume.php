<?php

namespace kalanis\UploadPerPartes\ServerKeys;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;


/**
 * Class Volume
 * @package kalanis\UploadPerPartes\ServerKeys
 * Connect shared key and local path in format which can be used in local volume
 */
class Volume extends AKey
{
    public function fromData(IDriverLocation $data): string
    {
        return $data->getDriverPrefix() . $data->getDriverKey();
    }
}
