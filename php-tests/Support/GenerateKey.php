<?php

namespace Support;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;
use kalanis\UploadPerPartes\GenerateKeys\AKey;
use kalanis\UploadPerPartes\Uploader\TargetSearch;


class GenerateKey extends AKey
{
    public function generateKey(IDriverLocation $data): string
    {
        return $data->getDriverKey() . TargetSearch::FILE_DRIVER_SUFF;
    }
}
