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

    /** @var CompositeAdapter */
    protected $lib = null;

    public function __construct(CompositeAdapter $lib, ?Interfaces\IUPPTranslations $lang = null)
    {
        $this->lib = $lib;
        parent::__construct($lang);
    }

    public function exists(string $key): bool
    {
        try {
            return $this->lib->exists(Stuff::pathToArray($key));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function load(string $key): string
    {
        try {
            return $this->toString($key, $this->lib->readFile(Stuff::pathToArray($key)));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotReadFile($key), $ex->getCode(), $ex);
        }
    }

    public function save(string $key, string $data): bool
    {
        try {
            return $this->lib->saveFile(Stuff::pathToArray($key), $data);
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key), $ex->getCode(), $ex);
        }
    }

    public function remove(string $key): bool
    {
        try {
            return $this->lib->deleteFile(Stuff::pathToArray($key));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key), $ex->getCode(), $ex);
        }
    }

    public function checkKeyClasses(object $limitData, object $storageKeys, object $infoFormat): bool
    {
        if (!$limitData instanceof Interfaces\InfoStorage\ForFiles) {
            throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong(get_class($limitData)));
        }
        if (!$storageKeys instanceof Interfaces\InfoStorage\ForFiles) {
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($storageKeys)));
        }
        if (!$infoFormat instanceof Interfaces\InfoStorage\ForFiles) {
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($infoFormat)));
        }
        return true;
    }
}
