<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\UploadException;


/**
 * Interface ITemporaryStorage
 * @package kalanis\UploadPerPartes\Interfaces
 * Processing in temporary storage
 */
interface ITemporaryStorage
{
    /**
     * @param string $path
     * @throws UploadException
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * @param string $path
     * @param int<0, max>|null $fromByte
     * @param int<0, max>|null $length
     * @throws UploadException
     * @return string
     */
    public function readData(string $path, ?int $fromByte, ?int $length): string;

    /**
     * @param string $path
     * @param int<0, max> $fromByte
     * @throws UploadException
     * @return bool
     */
    public function truncate(string $path, int $fromByte): bool;

    /**
     * @param string $path
     * @param string $content decoded content from client
     * @throws UploadException
     * @return bool
     */
    public function append(string $path, string $content): bool;

    /**
     * @param string $path
     * @throws UploadException
     * @return resource stream
     */
    public function readStream(string $path);

    /**
     * @param string $path
     * @throws UploadException
     * @return bool
     */
    public function remove(string $path): bool;
}
