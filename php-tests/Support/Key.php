<?php

namespace Support;


use kalanis\UploadPerPartes\Keys\AKey;
use kalanis\UploadPerPartes\Uploader\TargetSearch;


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
