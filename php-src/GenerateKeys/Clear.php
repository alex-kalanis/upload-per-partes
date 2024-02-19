<?php

namespace kalanis\UploadPerPartes\GenerateKeys;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;


/**
 * Class Clear
 * @package kalanis\UploadPerPartes\GenerateKeys
 */
class Clear extends AKey
{
    public function generateKey(IDriverLocation $data): string
    {
        return $data->getDriverKey();
    }
}
