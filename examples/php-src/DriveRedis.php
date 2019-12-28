<?php

namespace UploadPerPartes\examples;

use Config;
use Rc;
use UploadPerPartes;

/**
 * Class DriveRedis
 * @package UploadPerPartes\examples
 * Library for uploading files per partes - driving data are saved in Redis in JSON format
 */
class DriveRedis extends UploadPerPartes\DriveFile\ADriveFile
{
    /** @var null|Rc */
    protected $redis = null;
    /** @var int */
    protected $timeout = 0;

    public function __construct(UploadPerPartes\Translations $lang, string $path, Rc $redis, int $timeout = Config::CACHE_HOUR)
    {
        // path is not a route but redis key
        parent::__construct($lang, $path);
        $this->redis = $redis;
        $this->timeout = $timeout;
    }

    public function exists(): bool
    {
        // cannot call exists() - get on non-existing key returns false
        return (false !== $this->redis->get($this->getRedisKey()));
    }

    public function load(): UploadPerPartes\DriveFile\Data
    {
        $content = $this->redis->get($this->getRedisKey());
        $libData = new UploadPerPartes\DriveFile\Data;
        $jsonData = json_decode($content, true);
        foreach ($jsonData as $key => $value) {
            $libData->{$key} = $value;
        }
        return $libData->sanitizeData();
    }

    public function save(UploadPerPartes\DriveFile\Data $data): void
    {
        if (false === $this->redis->set($this->getRedisKey(), json_encode($data), $this->timeout)) {
            throw new UploadPerPartes\Exceptions\UploadException($this->lang->driveFileCannotWrite());
        }
    }

    public function remove()
    {
        if (!$this->redis->del($this->getRedisKey())) {
            throw new UploadPerPartes\Exceptions\UploadException($this->lang->driveFileCannotRemove());
        }
        return true;
    }

    protected function getRedisKey()
    {
        return 'aupload_content_' . $this->path;
    }
}