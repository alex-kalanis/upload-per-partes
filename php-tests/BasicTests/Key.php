<?php

namespace BasicTests;

use UploadPerPartes\Keys\AKey;
use UploadPerPartes\Storage\TargetSearch;
use UploadPerPartes\Uploader\Translations;

class Key extends AKey
{
    public function __construct(Translations $lang, TargetSearch $target)
    {
        parent::__construct($lang, $target);
    }

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