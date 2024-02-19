<?php

namespace kalanis\UploadPerPartes\GenerateKeys;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;


/**
 * Class Base64
 * @package kalanis\UploadPerPartes\GenerateKeys
 */
class Base64 extends AKey
{
    public function generateKey(IDriverLocation $data): string
    {
        return base64_encode($data->getDriverKey());
    }
}
