<?php

namespace kalanis\UploadPerPartes\Keys;


/**
 * Class Redis
 * @package kalanis\UploadPerPartes\Keys
 * Connect shared key and local in format available for Redis
 */
class Redis extends AKey
{
    const PREFIX = 'aupload_content_';

    public function fromSharedKey(string $key): string
    {
        return $this->getPrefix() . $key;
    }

    public function generateKeys(): parent
    {
        $this->sharedKey = md5($this->target->getFinalTargetName());
        return $this;
    }

    protected function getPrefix(): string
    {
        return static::PREFIX;
    }
}
