<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\kw_storage\Storage\Storage as lib;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class Storage
 * @package kalanis\UploadPerPartes\InfoStorage
 * Processing info file in kw_storage
 */
class Storage extends AStorage
{
    /** @var lib */
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
     */
    public function exists(string $key): bool
    {
        return $this->storage->exists($key);
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return string
     */
    public function load(string $key): string
    {
        try {
            return strval($this->storage->read($key));
        } catch (StorageException $ex) {
            throw new UploadException($this->lang->uppDriveFileCannotRead($key), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $key
     * @param string $data
     * @throws UploadException
     */
    public function save(string $key, string $data): void
    {
        try {
            if (false === $this->storage->write($key, $data, $this->timeout)) {
                throw new UploadException($this->lang->uppDriveFileCannotWrite($key));
            }
        } catch (StorageException $ex) {
            throw new UploadException($this->lang->uppDriveFileCannotWrite($key), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $key
     * @throws UploadException
     */
    public function remove(string $key): void
    {
        try {
            if (!$this->storage->remove($key)) {
                throw new UploadException($this->lang->uppDriveFileCannotRemove($key));
            }
        } catch (StorageException $ex) {
            throw new UploadException($this->lang->uppDriveFileCannotRemove($key), $ex->getCode(), $ex);
        }
    }
}
