<?php

namespace kalanis\UploadPerPartes\Interfaces;


/**
 * Interface IFinalKey
 * @package kalanis\UploadPerPartes\Interfaces
 * How to generate key for usage as storage key from passed data
 */
interface IStorageKey
{
    public function getKeyForStorage(string $what): string;
}
