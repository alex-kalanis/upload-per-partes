<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Uploader\Translations;


/**
 * Class AStorage
 * @package kalanis\UploadPerPartes\DataStorage
 * Target storage for data stream
 */
abstract class AStorage
{
    /** @var Translations */
    protected $lang = null;

    public function __construct(Translations $lang)
    {
        $this->lang = $lang;
    }

    /**
     * Add part to file
     * @param string $location
     * @param string $content binary content
     * @param int|null $seek where it save
     * @return void
     * @throws UploadException
     */
    abstract public function addPart(string $location, string $content, ?int $seek = null): void;

    /**
     * Get part of file
     * @param string $location
     * @param int $offset
     * @param int $limit
     * @return string
     * @throws UploadException
     */
    abstract public function getPart(string $location, int $offset, ?int $limit = null): string;

    /**
     * Truncate data file
     * @param string $location
     * @param int $offset
     * @return void
     * @throws UploadException
     */
    abstract public function truncate(string $location, int $offset): void;

    /**
     * Remove whole data file
     * @param string $location
     * @throws UploadException
     */
    abstract public function remove(string $location): void;
}
