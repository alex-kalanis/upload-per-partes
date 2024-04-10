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
 * - $localInfoStorage is Pass class
 * - $formatInfoInto and $limitDataForInternalKey is the same class (json, serialize)
 * - $keyEncoderForInternalStorage is clear pass
 * - $keyEncoderForExternalExchange is hex or b64 (go outside)
 *
 * Others with internal storage:
 * - $localInfoStorage is somewhere (not Pass)
 * - $formatInfoInto is something that pack/unpack info data for storage
 * - $limitDataForInternalKey is something that say which data will be used for key
 * - $keyEncoderForInternalStorage is something that generate the shared key itself
 * - $keyEncoderForExternalExchange is hex or b64 (go outside) - same as Pass
 */
class Processor
{
    use TLang;

    /** @var Interfaces\IInfoFormatting how to format info pack in storage */
    protected Interfaces\IInfoFormatting $formatInfoInto;
    /** @var Interfaces\IInfoStorage what storage will be used*/
    protected Interfaces\IInfoStorage $localInfoStorage;
    /** @var Interfaces\ILimitDataInternalKey how to modify data pack to be available for key creation */
    protected Interfaces\ILimitDataInternalKey $limitDataForInternalKey;
    /** @var Interfaces\IEncodeForInternalStorage how to hash data into key */
    protected Interfaces\IEncodeForInternalStorage $keyEncoderForInternalStorage;
    /** @var Interfaces\IEncodeForExternalExchange how to modify obtained key to share it without problems */
    protected Interfaces\IEncodeForExternalExchange $keyEncoderForExternalExchange;

    /**
     * @param Interfaces\IInfoFormatting $formatInfoInto
     * @param Interfaces\IInfoStorage $localInfoStorage
     * @param Interfaces\ILimitDataInternalKey $limitDataForInternalKey
     * @param Interfaces\IEncodeForInternalStorage $keyEncoderForInternalStorage
     * @param Interfaces\IEncodeForExternalExchange $keyEncoderForExternalExchange
     * @param Interfaces\IUPPTranslations|null $lang
     * @throws UploadException
     */
    public function __construct(
        Interfaces\IInfoFormatting $formatInfoInto,
        Interfaces\IInfoStorage $localInfoStorage,
        Interfaces\ILimitDataInternalKey $limitDataForInternalKey,
        Interfaces\IEncodeForInternalStorage $keyEncoderForInternalStorage,
        Interfaces\IEncodeForExternalExchange $keyEncoderForExternalExchange,
        ?Interfaces\IUPPTranslations $lang = null
    )
    {
        $localInfoStorage->checkKeyClasses($limitDataForInternalKey, $keyEncoderForInternalStorage, $formatInfoInto);
        $this->localInfoStorage = $localInfoStorage;
        $this->formatInfoInto = $formatInfoInto;
        $this->limitDataForInternalKey = $limitDataForInternalKey;
        $this->keyEncoderForInternalStorage = $keyEncoderForInternalStorage;
        $this->keyEncoderForExternalExchange = $keyEncoderForExternalExchange;
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
            $this->localInfoStorage->save($key, $this->formatInfoInto->toFormat($local));
        }
        return $this->keyEncoderForExternalExchange->pack($key);
    }

    public function getKey(Data $data): string
    {
        return $this->keyEncoderForInternalStorage->getKeyForStorage($this->limitDataForInternalKey->getLimitedData($data));
    }

    /**
     * @param string $serverData
     * @throws UploadException
     * @return Data
     */
    public function get(string $serverData): Data
    {
        return $this->getByKey($this->keyEncoderForExternalExchange->unpack($serverData));
    }

    /**
     * Delete drive file - usually on finish or discard
     * @param string $serverData
     * @throws UploadException
     * @return bool
     */
    public function remove(string $serverData): bool
    {
        $this->localInfoStorage->remove($this->keyEncoderForExternalExchange->unpack($serverData));
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
        return $this->localInfoStorage->exists($key);
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return Data
     */
    public function getByKey(string $key): Data
    {
        return $this->formatInfoInto->fromFormat($this->localInfoStorage->load($key));
    }
}
