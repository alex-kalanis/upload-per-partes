<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\PathsException;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class Files
 * @package kalanis\UploadPerPartes\InfoStorage
 * Processing info file on some file storage
 */
class Files extends AStorage
{
    use TToString;

    /** @var CompositeAdapter */
    protected $lib = null;

    public function __construct(CompositeAdapter $lib, ?IUPPTranslations $lang = null)
    {
        $this->lib = $lib;
        parent::__construct($lang);
    }

    public function exists(string $key): bool
    {
        try {
            return $this->lib->exists([$key]);
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function load(string $key): string
    {
        try {
            return $this->toString($key, $this->lib->readFile([$key]));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotReadFile($key), $ex->getCode(), $ex);
        }
    }

    public function save(string $key, string $data): void
    {
        try {
            $this->lib->saveFile([$key], $data);
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key), $ex->getCode(), $ex);
        }
    }

    public function remove(string $key): void
    {
        try {
            $this->lib->deleteFile([$key]);
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key), $ex->getCode(), $ex);
        }
    }
}
