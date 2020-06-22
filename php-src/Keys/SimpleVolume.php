<?php

namespace UploadPerPartes\Keys;

/**
 * Class Volume
 * @package UploadPerPartes\Keys
 * Connect shared key and local path in format which can be used in local volume
 */
class SimpleVolume extends AKey
{
    public function fromSharedKey(string $key): string
    {
        return base64_decode($key);
    }

    public function generateKeys(): parent
    {
        $this->sharedKey = base64_encode($this->target->getDriverLocation());
        return $this;
    }
}