<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;
use Predis as lib;


/**
 * Class Predis
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage
 * Processing info file in Redis
 * @codeCoverageIgnore
 */
class Predis implements Interfaces\IDrivingFile
{
    use TLang;

    protected lib\Client $redis;
    protected int $timeout = 0;

    public function __construct(lib\Client $redis, int $timeout = 3600, ?Interfaces\IUppTranslations $lang = null)
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
    public function get(string $key): string
    {
        return strval($this->redis->get($key));
    }

    /**
     * @param string $key
     * @param string $data
     * @return string
     * @codeCoverageIgnore
     */
    public function store(string $key, string $data): string
    {
        if (empty($this->timeout)) {
            $this->redis->set($key, $data);
        } else {
            $this->redis->set($key, $data, 'EX', $this->timeout);
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
