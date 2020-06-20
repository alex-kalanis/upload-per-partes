<?php

namespace UploadPerPartes\Keys;

/**
 * Class Redis
 * @package UploadPerPartes\Keys
 * Connect shared key and local in format available for Redis
 */
class Redis extends AKey
{
    public function fromShared(string $key): string
    {
        return $this->getRedisKey($key);
    }

    public function getNewSharedKey(): string
    {
        return md5($this->remoteFileName);
    }

    protected function getRedisKey($key): string
    {
        return 'aupload_content_' . $key;
    }
}