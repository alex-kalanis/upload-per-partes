<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\UploadException;


/**
 * Interface IContentDecoder
 * @package kalanis\UploadPerPartes\Interfaces
 * Decode content parts passed from client
 */
interface IContentDecoder
{
    /**
     * Which method it will use from client part - name of method
     * @return string
     */
    public function getMethod(): string;

    /**
     * Decoding itself
     * @param string $data
     * @throws UploadException
     * @return string
     */
    public function decode(string $data): string;
}
