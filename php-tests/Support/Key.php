<?php

namespace Support;

use UploadPerPartes\Keys\AKey;
use UploadPerPartes\Uploader\TargetSearch;

class Key extends AKey
{
    public function fromSharedKey(string $key): string
    {
        return 'php://memory';
    }

    public function generateKeys(): parent
    {
        $this->sharedKey = $this->target->getFinalTargetName() . TargetSearch::FILE_DRIVER_SUFF;
        return $this;
    }
}