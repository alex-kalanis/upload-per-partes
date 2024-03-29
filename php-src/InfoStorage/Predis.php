<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IInfoStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use Predis as lib;


/**
 * Class Predis
 * @package kalanis\UploadPerPartes\InfoStorage
 * Processing info file in Redis
 * @codeCoverageIgnore
 */
class Predis implements IInfoStorage
{
    use TLang;

    /** @var lib\Client */
    protected $redis = null;
    /** @var int */
    protected $timeout = 0;

    public function __construct(lib\Client $redis, ?int $timeout = 3600, ?IUPPTranslations $lang = null)
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
        return (0 < $this->redis->exists($key));
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
     * @codeCoverageIgnore
     */
    public function save(string $key, string $data): void
    {
        if (empty($this->timeout)) {
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
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key));
        }
    }
}
