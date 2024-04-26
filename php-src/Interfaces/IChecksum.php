<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\UploadException;


/**
 * Interface IChecksum
 * @package kalanis\UploadPerPartes\Interfaces
 * Checksums for parts requested by client
 */
interface IChecksum
{
    /**
     * Which method it will use from client part - name of method
     * @return string
     */
    public function getMethod(): string;

    /**
     * Calculation itself
     * @param string $data
     * @throws UploadException
     * @return string
     */
    public function checksum(string $data): string;
}
