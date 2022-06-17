<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class AStorage
 * @package kalanis\UploadPerPartes\DataStorage
 * Target storage for data stream
 */
abstract class AStorage
{
    /** @var IUPPTranslations */
    protected $lang = null;

    public function __construct(IUPPTranslations $lang)
    {
        $this->lang = $lang;
    }

    /**
     * If that file exists
     * @param string $location
     * @throws UploadException
     * @return bool
     */
    abstract public function exists(string $location): bool;

    /**
     * Add part to file
     * @param string $location
     * @param string $content binary content
     * @param int<0, max>|null $seek where it save
     * @throws UploadException
     * @return void
     */
    abstract public function addPart(string $location, string $content, ?int $seek = null): void;

    /**
     * Get part of file
     * @param string $location
     * @param int<0, max> $offset
     * @param int<0, max>|null $limit
     * @throws UploadException
     * @return string
     */
    abstract public function getPart(string $location, int $offset, ?int $limit = null): string;

    /**
     * Truncate data file
     * @param string $location
     * @param int<0, max> $offset
     * @throws UploadException
     * @return void
     */
    abstract public function truncate(string $location, int $offset): void;

    /**
     * Remove whole data file
     * @param string $location
     * @throws UploadException
     */
    abstract public function remove(string $location): void;
}
