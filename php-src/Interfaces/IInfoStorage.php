<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class IInfoStorage
 * @package kalanis\UploadPerPartes\Interfaces
 * Target storage for data stream
 */
interface IInfoStorage
{
    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * @param string $key
     * @throws UploadException
     * @return string
     */
    public function load(string $key): string;

    /**
     * @param string $key
     * @param string $data
     * @throws UploadException
     * @return bool
     */
    public function save(string $key, string $data): bool;

    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function remove(string $key): bool;

    /**
     * The classes passed there must adhere 2 interfaces - about processing and about possible usage with storage
     * This check is about storage part
     * @param object $limitData
     * @param object $storageKeys
     * @param object $infoFormat
     * @throws UploadException
     * @return bool
     */
    public function checkKeyClasses(object $limitData, object $storageKeys, object $infoFormat): bool;
}
