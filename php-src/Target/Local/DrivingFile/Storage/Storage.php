<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage;


use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Storage
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage
 * Storing driving file data in simple storage represented by kw_storage package
 */
class Storage implements Interfaces\IDrivingFile
{
    use TLang;

    protected IStorage $storage;
    protected string $keyPrefix;

    public function __construct(IStorage $storage, string $keyPrefix = '', ?Interfaces\IUppTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->keyPrefix = $keyPrefix;
        $this->setUppLang($lang);
    }

    public function exists(string $key): bool
    {
        try {
            return $this->storage->exists($this->fullPath($key));
        } catch (StorageException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function store(string $key, string $data): string
    {
        try {
            if (!$this->storage->write($this->fullPath($key), $data)) {
                throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
            }
            return $key;
        } catch (StorageException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function get(string $key): string
    {
        try {
            return $this->storage->read($this->fullPath($key));
        } catch (StorageException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function remove(string $key): bool
    {
        try {
            return $this->storage->remove($this->fullPath($key));
        } catch (StorageException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $key
     * @return string
     */
    protected function fullPath(string $key): string
    {
        return $this->keyPrefix . $key;
    }

    public function checkKeyEncoder(DrivingFile\KeyEncoders\AEncoder $encoder): bool
    {
        if (!$encoder instanceof Interfaces\Storages\ForStorage) {
            throw new UploadException($this->getUppLang()->uppKeyEncoderVariantIsWrong(get_class($encoder)));
        }
        return true;
    }
}
