<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IInfoStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use Redis as lib;


/**
 * Class Redis
 * @package kalanis\UploadPerPartes\InfoStorage
 * Processing info file in Redis
 * @codeCoverageIgnore
 */
class Redis implements IInfoStorage
{
    /** @var IUPPTranslations */
    protected $lang = null;
    /** @var lib */
    protected $redis = null;
    /** @var int */
    protected $timeout = 0;

    public function __construct(IUPPTranslations $lang, lib $redis, int $timeout = 3600)
    {
        // path is not a route but redis key
        $this->lang = $lang;
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
    public function load(string $key): string
    {
        return strval($this->redis->get($key));
    }

    /**
     * @param string $key
     * @param string $data
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function save(string $key, string $data): void
    {
        if (false === $this->redis->set($key, $data, $this->timeout)) {
            throw new UploadException($this->lang->uppDriveFileCannotWrite($key));
        }
    }

    /**
     * @param string $key
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function remove(string $key): void
    {
        if (!$this->redis->del($key)) {
            throw new UploadException($this->lang->uppDriveFileCannotRemove($key));
        }
    }
}
