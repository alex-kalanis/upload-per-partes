<?php

namespace kalanis\UploadPerPartes\GenerateKeys;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;


/**
 * Class Md5
 * @package kalanis\UploadPerPartes\GenerateKeys
 */
class Md5 extends AKey
{
    public function generateKey(IDriverLocation $data): string
    {
        return md5($data->getDriverKey());
    }
}
