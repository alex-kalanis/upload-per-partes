<?php

namespace kalanis\UploadPerPartes\Keys;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class Volume
 * @package kalanis\UploadPerPartes\Keys
 * Connect shared key and local path in format which can be used in local volume
 */
class SimpleVolume extends AKey
{
    public function fromSharedKey(string $key): string
    {
        $result = base64_decode($key, true);
        if (false === $result) {
            throw new UploadException($this->lang->uppSharedKeyIsInvalid());
        }
        return $result;
    }

    public function generateKeys(): parent
    {
        $this->sharedKey = base64_encode($this->target->getDriverLocation());
        return $this;
    }
}
