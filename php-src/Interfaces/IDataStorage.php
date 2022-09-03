<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Interface IDataStorage
 * @package kalanis\UploadPerPartes\Interfaces
 * Target storage for data stream
 */
interface IDataStorage
{
    /**
     * If that file exists
     * @param string $location
     * @throws UploadException
     * @return bool
     */
    public function exists(string $location): bool;

    /**
     * Add part to file
     * @param string $location
     * @param string $content binary content
     * @param int<0, max>|null $seek where it save
     * @throws UploadException
     * @return void
     */
    public function addPart(string $location, string $content, ?int $seek = null): void;

    /**
     * Get part of file
     * @param string $location
     * @param int<0, max> $offset
     * @param int<0, max>|null $limit
     * @throws UploadException
     * @return string
     */
    public function getPart(string $location, int $offset, ?int $limit = null): string;

    /**
     * Truncate data file
     * @param string $location
     * @param int<0, max> $offset
     * @throws UploadException
     * @return void
     */
    public function truncate(string $location, int $offset): void;

    /**
     * Remove whole data file
     * @param string $location
     * @throws UploadException
     */
    public function remove(string $location): void;
}
