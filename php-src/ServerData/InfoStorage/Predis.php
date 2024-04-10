<?php

namespace kalanis\UploadPerPartes\ServerData\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;
use Predis as lib;


/**
 * Class Predis
 * @package kalanis\UploadPerPartes\ServerData\InfoStorage
 * Processing info file in Redis
 * @codeCoverageIgnore
 */
class Predis implements Interfaces\IInfoStorage
{
    use TLang;

    protected lib\Client $redis;
    protected int $timeout = 0;

    public function __construct(lib\Client $redis, int $timeout = 3600, ?Interfaces\IUPPTranslations $lang = null)
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
     * @return bool
     * @codeCoverageIgnore
     */
    public function save(string $key, string $data): bool
    {
        if (empty($this->timeout)) {
            $this->redis->set($key, $data);
        } else {
            $this->redis->set($key, $data, 'EX', $this->timeout);
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

    /**
     * @param object $limitDataForKey
     * @param object $storageKeys
     * @param object $storedInfoAs
     * @throws UploadException
     * @return bool
     * @codeCoverageIgnore
     */
    public function checkKeyClasses(object $limitDataForKey, object $storageKeys, object $storedInfoAs): bool
    {
        if (!$limitDataForKey instanceof Interfaces\InfoStorage\ForKV) {
            throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong(get_class($limitDataForKey)));
        }
        if (!$storageKeys instanceof Interfaces\InfoStorage\ForKV) {
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($storageKeys)));
        }
        if (!$storedInfoAs instanceof Interfaces\InfoStorage\ForKV) {
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($storedInfoAs)));
        }
        return true;
    }
}
