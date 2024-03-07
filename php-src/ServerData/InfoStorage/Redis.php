<?php

namespace kalanis\UploadPerPartes\ServerData\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;
use Redis as lib;


/**
 * Class Redis
 * @package kalanis\UploadPerPartes\ServerData\InfoStorage
 * Processing info file in Redis
 * @codeCoverageIgnore
 */
class Redis implements Interfaces\IInfoStorage
{
    use TLang;

    /** @var lib */
    protected $redis = null;
    /** @var int */
    protected $timeout = 0;

    public function __construct(lib $redis, int $timeout = 3600, ?Interfaces\IUPPTranslations $lang = null)
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
    public function load(string $key): string
    {
        return strval($this->redis->get($key));
    }

    /**
     * @param string $key
     * @param string $data
     * @throws UploadException
     * @return bool
     * @codeCoverageIgnore
     */
    public function save(string $key, string $data): bool
    {
        if (false === $this->redis->set($key, $data, $this->timeout)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
        }
        return true;
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     * @codeCoverageIgnore
     */
    public function remove(string $key): bool
    {
        if (!$this->redis->del($key)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key));
        }
        return true;
    }

    public function checkKeyClasses(object $limitData, object $storageKeys, object $infoFormat): bool
    {
        if (!$limitData instanceof Interfaces\InfoStorage\ForKV) {
            throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong(get_class($limitData)));
        }
        if (!$storageKeys instanceof Interfaces\InfoStorage\ForKV) {
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($storageKeys)));
        }
        if (!$infoFormat instanceof Interfaces\InfoStorage\ForKV) {
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($infoFormat)));
        }
        return true;
    }
}
