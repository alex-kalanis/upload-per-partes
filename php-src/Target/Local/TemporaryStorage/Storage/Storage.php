<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage\Storage;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToStream;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Interfaces\ITemporaryStorage;
use kalanis\UploadPerPartes\Interfaces\IUppTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Storage
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage\Storage
 * Storing driving file data in simple storage represented by kw_storage package
 */
class Storage implements ITemporaryStorage
{
    use TLang;
    use TToStream;

    protected IStorage $storage;
    protected string $keyPrefix = '';

    public function __construct(IStorage $storage, string $keyPrefix = '', IUppTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->keyPrefix = $keyPrefix;
        $this->setUppLang($lang);
    }

    public function exists(string $path): bool
    {
        try {
            return $this->storage->exists($this->fullPath($path));
        } catch (StorageException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readData(string $path, ?int $fromByte, ?int $length): string
    {
        try {
            $fullPath = $this->fullPath($path);
            $current = $this->storage->exists($fullPath) ? $this->storage->read($fullPath) : '';
            return is_null($length)
                ? substr($current, intval($fromByte))
                : substr($current, intval($fromByte), $length)
            ;
        } catch (StorageException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function truncate(string $path, int $fromByte): bool
    {
        try {
            $fullPath = $this->fullPath($path);
            $current = $this->storage->exists($fullPath) ? $this->storage->read($fullPath) : '';
            return $this->storage->write($fullPath, substr($current, 0, $fromByte));
        } catch (StorageException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function append(string $path, string $content): bool
    {
        try {
            $fullPath = $this->fullPath($path);
            $current = $this->storage->exists($fullPath) ? $this->storage->read($fullPath) : '';
            return $this->storage->write($fullPath, $current . $content);
        } catch (StorageException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readStream(string $path)
    {
        try {
            $fullPath = $this->fullPath($path);
            $current = $this->storage->exists($fullPath) ? $this->storage->read($fullPath) : '';
            return $this->toStream($path, $current);
        } catch (FilesException | StorageException $ex) {
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

    protected function fullPath(string $path): string
    {
        return $this->keyPrefix . $path;
    }
}
