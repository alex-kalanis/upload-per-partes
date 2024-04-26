<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\UploadException;


/**
 * Interface IDrivingFile
 * @package kalanis\UploadPerPartes\Interfaces
 * Work with driving file and its shared key
 */
interface IDrivingFile
{
    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * @param string $key
     * @param string $data
     * @throws UploadException
     * @return string shared key used for storing data (usually $key)
     */
    public function store(string $key, string $data): string;

    /**
     * @param string $key
     * @throws UploadException
     * @return string
     */
    public function get(string $key): string;

    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function remove(string $key): bool;

    /**
     * @param DrivingFile\KeyEncoders\AEncoder $encoder
     * @throws UploadException
     * @return bool
     */
    public function checkKeyEncoder(DrivingFile\KeyEncoders\AEncoder $encoder): bool;
}
