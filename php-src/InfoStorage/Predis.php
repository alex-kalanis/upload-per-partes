<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use Predis as lib;
use kalanis\UploadPerPartes\Uploader\Translations;
use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class Predis
 * @package kalanis\UploadPerPartes\InfoStorage
 * Processing info file in Redis
 * @codeCoverageIgnore
 */
class Predis extends AStorage
{
    /** @var null|lib\Client */
    protected $redis = null;
    /** @var int */
    protected $timeout = 0;

    public function __construct(Translations $lang, lib\Client $redis, int $timeout = 3600)
    {
        // path is not a route but redis key
        parent::__construct($lang);
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
        return (0 < $this->redis->exists($key));
    }

    /**
     * @param string $key
     * @return string
     * @codeCoverageIgnore
     */
    public function load(string $key): string
    {
        return (string)$this->redis->get($key);
    }

    /**
     * @param string $key
     * @param string $data
     * @codeCoverageIgnore
     */
    public function save(string $key, string $data): void
    {
        if (is_null($this->timeout)) {
            $this->redis->set($key, $data);
        } else {
            $this->redis->set($key, $data, 'EX', $this->timeout);
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
            throw new UploadException($this->lang->driveFileCannotRemove());
        }
    }
}
