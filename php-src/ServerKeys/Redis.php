<?php

namespace kalanis\UploadPerPartes\ServerKeys;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;


/**
 * Class Redis
 * @package kalanis\UploadPerPartes\ServerKeys
 * Connect shared key and local in format available for Redis
 */
class Redis extends AKey
{
    const PREFIX = 'aupload_content_';

    public function fromData(IDriverLocation $data): string
    {
        return $this->getPrefix() . $data->getDriverKey();
    }

    protected function getPrefix(): string
    {
        return static::PREFIX;
    }
}
