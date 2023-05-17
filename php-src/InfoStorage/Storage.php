<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IInfoStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class Storage
 * @package kalanis\UploadPerPartes\InfoStorage
 * Processing info file in kw_storage
 */
class Storage implements IInfoStorage
{
    use TLang;

    /** @var IStorage */
    protected $storage = null;
    /** @var int */
    protected $timeout = 0;

    public function __construct(IStorage $storage, int $timeout = 3600, IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
        $this->storage = $storage;
        $this->timeout = $timeout;
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function exists(string $key): bool
    {
        try {
            return $this->storage->exists($key);
        } catch (StorageException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRead($key), $ex->getCode(), $ex);
        }
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
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRead($key), $ex->getCode(), $ex);
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
                throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
            }
        } catch (StorageException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key), $ex->getCode(), $ex);
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
                throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key));
            }
        } catch (StorageException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key), $ex->getCode(), $ex);
        }
    }
}
