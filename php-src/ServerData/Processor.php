<?php

namespace kalanis\UploadPerPartes\ServerData;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class Processor
 * @package kalanis\UploadPerPartes\ServerData
 * Process metadata from server and client to get info about upload
 *
 * 4 modifiers ->
 * - 2x internal keys (filter and hash)
 * - 1x the data itself
 * - 1x key to outer world
 * 1 storage which knows, what it can process without problems (not every storage can use any key)
 *
 * Pass style - no real info data storage, everything go to the client:
 * - $infoStorage is Pass class
 * - $infoFormat and $limitData is the same class (json, serialize)
 * - $storageKey is clear pass
 * - $encodeKey is hex or b64 (go outside)
 *
 * Others with internal storage:
 * - $infoStorage is somewhere (not Pass)
 * - $infoFormat is something that pack/unpack info data for storage
 * - $limitData is something that say which data will be used for key
 * - $storageKey is something that generate the shared key itself
 * - $encodeKey is hex or b64 (go outside) - same as Pass
 */
class Processor
{
    use TLang;

    /** @var Interfaces\IInfoFormatting how to format info pack in storage */
    protected $infoFormat = null;
    /** @var Interfaces\IInfoStorage what storage will be used*/
    protected $infoStorage = null;
    /** @var Interfaces\ILimitPassedData how to modify data pack to be available for key creation */
    protected $limitData = null;
    /** @var Interfaces\IStorageKey how to hash data into key */
    protected $storageKeys = null;
    /** @var Interfaces\IEncodeSharedKey how to modify obtained key to share it without problems */
    protected $encodedKey = null;

    /**
     * @param Interfaces\IInfoFormatting $infoFormat
     * @param Interfaces\IInfoStorage $infoStorage
     * @param Interfaces\ILimitPassedData $limitData
     * @param Interfaces\IStorageKey $storageKeys
     * @param Interfaces\IEncodeSharedKey $encodedKey
     * @param Interfaces\IUPPTranslations|null $lang
     * @throws UploadException
     */
    public function __construct(
        Interfaces\IInfoFormatting $infoFormat,
        Interfaces\IInfoStorage $infoStorage,
        Interfaces\ILimitPassedData $limitData,
        Interfaces\IStorageKey $storageKeys,
        Interfaces\IEncodeSharedKey $encodedKey,
        ?Interfaces\IUPPTranslations $lang = null
    )
    {
        $infoStorage->checkKeyClasses($limitData, $storageKeys, $infoFormat);
        $this->infoStorage = $infoStorage;
        $this->infoFormat = $infoFormat;
        $this->limitData = $limitData;
        $this->storageKeys = $storageKeys;
        $this->encodedKey = $encodedKey;
        $this->setUppLang($lang);
    }

    /**
     * @param Data $local
     * @param bool $saveToStorage
     * @throws UploadException
     * @return string
     */
    public function store(Data $local, bool $saveToStorage = true): string
    {
        $key = $this->getKey($local);
        if ($saveToStorage) {
            $this->infoStorage->save($key, $this->infoFormat->toFormat($local));
        }
        return $this->encodedKey->pack($key);
    }

    public function getKey(Data $data): string
    {
        return $this->storageKeys->getKeyForStorage($this->limitData->getLimitedData($data));
    }

    /**
     * @param string $serverData
     * @throws UploadException
     * @return Data
     */
    public function get(string $serverData): Data
    {
        return $this->getByKey($this->encodedKey->unpack($serverData));
    }

    /**
     * Delete drive file - usually on finish or discard
     * @param string $serverData
     * @throws UploadException
     * @return bool
     */
    public function remove(string $serverData): bool
    {
        $this->infoStorage->remove($this->encodedKey->unpack($serverData));
        return true;
    }

    /**
     * Has driver data? Mainly for testing
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function existsByKey(string $key): bool
    {
        return $this->infoStorage->exists($key);
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return Data
     */
    public function getByKey(string $key): Data
    {
        return $this->infoFormat->fromFormat($this->infoStorage->load($key));
    }
}
