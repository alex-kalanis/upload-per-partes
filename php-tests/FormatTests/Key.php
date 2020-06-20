<?php

namespace FormatTests;

use UploadPerPartes\Keys\AKey;
use UploadPerPartes\Uploader\Translations;

class Key extends AKey
{
    protected $tempDir = null;

    public function __construct(Translations $key)
    {
        parent::__construct($key);
        $this->tempDir = realpath('/tmp/') . '/';
    }

    public function fromShared(string $key): string
    {
        return $this->tempDir . base64_decode($key);
    }

    public function getNewSharedKey(): string
    {
        return base64_encode($this->fileName);
    }
}