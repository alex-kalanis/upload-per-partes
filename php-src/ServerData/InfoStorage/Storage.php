<?php

namespace kalanis\UploadPerPartes\ServerData\InfoStorage;


use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class Storage
 * @package kalanis\UploadPerPartes\ServerData\InfoStorage
 * Processing info file in kw_storage
 */
class Storage implements Interfaces\IInfoStorage
{
    use TLang;

    protected IStorage $storage;
    protected int $timeout = 0;

    public function __construct(IStorage $storage, int $timeout = 3600, ?Interfaces\IUPPTranslations $lang = null)
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
     * @return bool
     */
    public function save(string $key, string $data): bool
    {
        try {
            if (false === $this->storage->write($key, $data, $this->timeout)) {
                throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
            }
            return true;
        } catch (StorageException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return bool
     */
    public function remove(string $key): bool
    {
        try {
            if (!$this->storage->remove($key)) {
                throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key));
            }
            return true;
        } catch (StorageException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key), $ex->getCode(), $ex);
        }
    }

    public function checkKeyClasses(object $limitDataForKey, object $storageKeys, object $storedInfoAs): bool
    {
        if (!$limitDataForKey instanceof Interfaces\InfoStorage\ForStorage) {
            throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong(get_class($limitDataForKey)));
        }
        if (!$storageKeys instanceof Interfaces\InfoStorage\ForStorage) {
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($storageKeys)));
        }
        if (!$storedInfoAs instanceof Interfaces\InfoStorage\ForStorage) {
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($storedInfoAs)));
        }
        return true;
    }
}
