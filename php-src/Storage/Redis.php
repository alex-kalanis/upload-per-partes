<?php

namespace UploadPerPartes\Storage;

use Rc;
use UploadPerPartes\Translations;
use UploadPerPartes\Exceptions\UploadException;

/**
 * Class DriveRedis
 * @package UploadPerPartes\Storage
 * Processing info file in Redis cluster
 */
class Redis extends AStorage
{
    /** @var null|Rc */
    protected $redis = null;
    /** @var int */
    protected $timeout = 0;

    public function __construct(Translations $lang, Rc $redis, int $timeout = 3600)
    {
        // path is not a route but redis key
        parent::__construct($lang);
        $this->redis = $redis;
        $this->timeout = $timeout;
    }

    public function exists(string $key): bool
    {
        // cannot call exists() - get on non-existing key returns false
        return (false !== $this->redis->get($key));
    }

    public function load(string $key): string
    {
        return (string)$this->redis->get($key);
    }

    public function save(string $key, string $data): void
    {
        if (false === $this->redis->set($key, $data, $this->timeout)) {
            throw new UploadException($this->lang->driveFileCannotWrite());
        }
    }

    public function remove(string $key): void
    {
        if (!$this->redis->del($key)) {
            throw new UploadException($this->lang->driveFileCannotRemove());
        }
    }
}