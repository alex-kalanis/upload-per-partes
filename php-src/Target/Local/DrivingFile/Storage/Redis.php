<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;
use Redis as lib;


/**
 * Class Redis
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage
 * Processing info file in Redis
 * @codeCoverageIgnore
 */
class Redis implements Interfaces\IDrivingFile
{
    use TLang;

    protected lib $redis;
    protected int $timeout = 0;

    public function __construct(lib $redis, int $timeout = 3600, ?Interfaces\IUppTranslations $lang = null)
    {
        // path is not a route but redis key
        $this->setUppLang($lang);
        $this->redis = $redis;
        $this->timeout = $timeout;
    }

    /**
     * @param string $key
     * @return bool
     * @codeCoverageIgnore
     */
    public function exists(string $key): bool
    {
        // cannot call exists() - get on non-existing key returns false
        return (false !== $this->redis->get($key));
    }

    /**
     * @param string $key
     * @return string
     * @codeCoverageIgnore
     */
    public function get(string $key): string
    {
        return strval($this->redis->get($key));
    }

    /**
     * @param string $key
     * @param string $data
     * @throws UploadException
     * @return string
     * @codeCoverageIgnore
     */
    public function store(string $key, string $data): string
    {
        if (false === $this->redis->set($key, $data, $this->timeout)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
        }
        return $key;
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     * @codeCoverageIgnore
     */
    public function remove(string $key): bool
    {
        return boolval($this->redis->del($key));
    }

    public function checkKeyEncoder(DrivingFile\KeyEncoders\AEncoder $encoder): bool
    {
        if (!$encoder instanceof Interfaces\Storages\ForKV) {
            throw new UploadException($this->getUppLang()->uppKeyEncoderVariantIsWrong(get_class($encoder)));
        }
        return true;
    }
}
