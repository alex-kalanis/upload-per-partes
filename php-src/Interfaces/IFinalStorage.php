<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\UploadException;


/**
 * Interface IFinalStorage
 * @package kalanis\UploadPerPartes\Interfaces
 * Where it will be stored in final step
 */
interface IFinalStorage
{
    /**
     * @param string $path
     * @throws UploadException
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * @param string $path
     * @param resource $source
     * @throws UploadException
     * @return bool
     */
    public function store(string $path, $source): bool;

    /**
     * @param string $key
     * @throws UploadException
     * @return string
     */
    public function findName(string $key): string;
}
