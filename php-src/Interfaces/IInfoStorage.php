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
     */
    public function save(string $key, string $data): void;

    /**
     * @param string $key
     * @throws UploadException
     */
    public function remove(string $key): void;
}
