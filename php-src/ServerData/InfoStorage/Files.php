<?php

namespace kalanis\UploadPerPartes\ServerData\InfoStorage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;


/**
 * Class Files
 * @package kalanis\UploadPerPartes\ServerData\InfoStorage
 * Processing info file on some file storage
 */
class Files extends AStorage
{
    use TToString;

    protected CompositeAdapter $lib;
    /** @var string[] */
    protected array $onPath = [];

    /**
     * @param CompositeAdapter $lib
     * @param string[] $onPath
     * @param Interfaces\IUPPTranslations|null $lang
     */
    public function __construct(CompositeAdapter $lib, array $onPath = [], ?Interfaces\IUPPTranslations $lang = null)
    {
        $this->lib = $lib;
        $this->onPath = $onPath;
        parent::__construct($lang);
    }

    public function exists(string $key): bool
    {
        try {
            return $this->lib->exists(array_merge($this->onPath, Stuff::pathToArray($key)));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function load(string $key): string
    {
        try {
            return $this->toString($key, $this->lib->readFile(array_merge($this->onPath, Stuff::pathToArray($key))));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotReadFile($key), $ex->getCode(), $ex);
        }
    }

    public function save(string $key, string $data): bool
    {
        try {
            return $this->lib->saveFile(array_merge($this->onPath, Stuff::pathToArray($key)), $data);
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key), $ex->getCode(), $ex);
        }
    }

    public function remove(string $key): bool
    {
        try {
            return $this->lib->deleteFile(array_merge($this->onPath, Stuff::pathToArray($key)));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key), $ex->getCode(), $ex);
        }
    }

    public function checkKeyClasses(object $limitDataForKey, object $storageKeys, object $storedInfoAs): bool
    {
        if (!$limitDataForKey instanceof Interfaces\InfoStorage\ForFiles) {
            throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong(get_class($limitDataForKey)));
        }
        if (!$storageKeys instanceof Interfaces\InfoStorage\ForFiles) {
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($storageKeys)));
        }
        if (!$storedInfoAs instanceof Interfaces\InfoStorage\ForFiles) {
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($storedInfoAs)));
        }
        return true;
    }
}
