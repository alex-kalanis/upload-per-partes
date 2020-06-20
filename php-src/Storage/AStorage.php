<?php

namespace UploadPerPartes\Storage;

use UploadPerPartes\Exceptions\UploadException;
use UploadPerPartes\Translations;

/**
 * Class AStorage
 * @package UploadPerPartes\Storage
 * Target storage for data stream
 */
abstract class AStorage
{
    protected $lang = null;

    public function __construct(Translations $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param string $key
     * @return bool
     */
    abstract public function exists(string $key): bool;

    /**
     * @param string $key
     * @return string
     * @throws UploadException
     */
    abstract public function load(string $key): string;

    /**
     * @param string $key
     * @param string $data
     * @throws UploadException
     */
    abstract public function save(string $key, string $data): void;

    /**
     * @param string $key
     * @throws UploadException
     */
    abstract public function remove(string $key): void;
}