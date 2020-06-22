<?php

namespace FormatTests;

use UploadPerPartes\Keys\AKey;
use UploadPerPartes\Storage\TargetSearch;
use UploadPerPartes\Uploader\Translations;

class Key extends AKey
{
    protected $tempDir = null;

    public function __construct(Translations $key, TargetSearch $target)
    {
        parent::__construct($key, $target);
        $this->tempDir = realpath('/tmp/') . '/';
    }

    public function fromSharedKey(string $key): string
    {
        return $this->tempDir . base64_decode($key);
    }

    public function generateKeys(): parent
    {
        $this->sharedKey = base64_encode($this->target->getFinalTargetName() . TargetSearch::FILE_DRIVER_SUFF);
        return $this;
    }
}