<?php

namespace UploadPerPartes\Keys;

/**
 * Class Redis
 * @package UploadPerPartes\Keys
 * Connect shared key and local in format available for Redis
 */
class Volume extends AKey
{
    public function fromShared(string $key): string
    {
        $this->checkTargetDir();
        return $this->targetDir . $key;
    }
}