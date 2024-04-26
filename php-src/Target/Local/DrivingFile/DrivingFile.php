<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile;


use kalanis\UploadPerPartes\Interfaces\IDrivingFile;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class DrivingFile
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile
 * Driving file actions in one package
 */
class DrivingFile
{
    protected IDrivingFile $storage;
    protected KeyEncoders\AEncoder $keyEncoder;
    protected KeyModifiers\AModifier $keyModifier;
    protected DataEncoders\AEncoder $dataEncoder;
    protected DataModifiers\AModifier $dataModifier;

    public function __construct(
        IDrivingFile $storage,
        KeyEncoders\AEncoder $keyEncoder,
        KeyModifiers\AModifier $keyModifier,
        DataEncoders\AEncoder $dataEncoder,
        DataModifiers\AModifier $dataModifier
    )
    {
        $this->storage = $storage;
        $this->keyEncoder = $keyEncoder; // how to encode shared key before usage on storage
        $this->keyModifier = $keyModifier; // how to modify encoded shared key before usage on storage
        $this->dataEncoder = $dataEncoder; // how to encode data before they went to storage
        $this->dataModifier = $dataModifier; // how to modify encoded data before they went to storage
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return bool
     */
    public function existsByData(Data $data): bool
    {
        return $this->existsByKey($this->keyByData($data));
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    public function keyByData(Data $data): string
    {
        return $this->keyModifier->pack($this->keyEncoder->encode($data));
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function existsByKey(string $key): bool
    {
        return $this->storage->exists($key);
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    public function storeByData(Data $data): string
    {
        return $this->storeByKey($this->keyByData($data), $data);
    }

    /**
     * @param string $key
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    public function storeByKey(string $key, Data $data): string
    {
        return $this->storage->store($key, $this->dataModifier->pack($this->dataEncoder->pack($data)));
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return Data
     */
    public function get(string $key): Data
    {
        return $this->dataEncoder->unpack($this->dataModifier->unpack($this->storage->get($key)));
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return bool
     */
    public function removeByData(Data $data): bool
    {
        return $this->removeByKey($this->keyByData($data));
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function removeByKey(string $key): bool
    {
        return $this->storage->remove($key);
    }
}
