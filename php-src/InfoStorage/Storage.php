<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\kw_storage\Storage as lib;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class Storage
 * @package kalanis\UploadPerPartes\InfoStorage
 * Processing info file in kw_storage
 * @codeCoverageIgnore
 */
class Storage extends AStorage
{
    /** @var null|lib */
    protected $storage = null;
    /** @var int */
    protected $timeout = 0;

    public function __construct(IUPPTranslations $lang, lib $storage, int $timeout = 3600)
    {
        // path is not a route but redis key
        parent::__construct($lang);
        $this->storage = $storage;
        $this->timeout = $timeout;
    }

    /**
     * @param string $key
     * @return bool
     * @throws StorageException
     * @codeCoverageIgnore
     */
    public function exists(string $key): bool
    {
        return $this->storage->exists($key);
    }

    /**
     * @param string $key
     * @return string
     * @throws StorageException
     * @codeCoverageIgnore
     */
    public function load(string $key): string
    {
        return (string)$this->storage->get($key);
    }

    /**
     * @param string $key
     * @param string $data
     * @throws StorageException
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function save(string $key, string $data): void
    {
        if (false === $this->storage->set($key, $data, $this->timeout)) {
            throw new UploadException($this->lang->uppDriveFileCannotWrite($key));
        }
    }

    /**
     * @param string $key
     * @throws StorageException
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function remove(string $key): void
    {
        if (!$this->storage->delete($key)) {
            throw new UploadException($this->lang->uppDriveFileCannotRemove($key));
        }
    }
}
