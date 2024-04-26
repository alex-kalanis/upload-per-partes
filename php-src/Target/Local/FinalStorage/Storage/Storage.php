<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\Storage;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Interfaces\IFinalStorage;
use kalanis\UploadPerPartes\Interfaces\IUppTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Storage
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\Storage
 * Where to store data on target destination - storage based on kw_storage
 */
class Storage implements IFinalStorage
{
    use TLang;
    use TToString;

    protected IStorage $storage;
    protected string $keyPrefix = '';

    public function __construct(IStorage $storage, string $keyPrefix = '', IUppTranslations $lang = null)
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

    public function store(string $path, $data): bool
    {
        try {
            return $this->storage->write($this->fullPath($path), $this->toString($path, $data));
        } catch (FilesException | StorageException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotWriteFile($path), $ex->getCode(), $ex);
        }
    }

    public function findName(string $key): string
    {
        if (!$this->exists($key)) {
            return $key;
        }

        $name = Stuff::fileBase($key);
        $suffix = Stuff::fileExt($key);

        $i = 0;
        while ($this->exists($name . $this->getNameSeparator() . strval($i) . $suffix)) {
            $i++;
        }
        return $name . $this->getNameSeparator() . strval($i) . $suffix;
    }

    protected function fullPath(string $key): string
    {
        return $this->keyPrefix . $key;
    }

    protected function getNameSeparator(): string
    {
        return '__';
    }
}
