<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class Files
 * @package kalanis\UploadPerPartes\DataStorage
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

    public function exists(string $location): bool
    {
        try {
            return $this->lib->exists(Stuff::pathToArray($location));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function addPart(string $location, string $content, ?int $seek = null): void
    {
        try {
            $path = Stuff::pathToArray($location);
            if (is_null($seek)) {
                // null here is to amend on the file end, on files it means total rewrite
                $seek = $this->lib->size($path);
                if (is_null($seek)) {
                    // hot way - no seek
                    $seek = strlen($this->toString($location, $this->lib->readFile($path)));
                }
            }
            $this->lib->saveFile($path, $content, abs($seek));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function getPart(string $location, int $offset, ?int $limit = null): string
    {
        try {
            return $this->toString($location, $this->lib->readFile(Stuff::pathToArray($location), $offset, $limit));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotReadFile($location), $ex->getCode(), $ex);
        }
    }

    public function truncate(string $location, int $offset): void
    {
        try {
            $path = Stuff::pathToArray($location);
            $this->lib->saveFile($path, $this->lib->readFile($path, 0, $offset));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotTruncateFile($location), $ex->getCode(), $ex);
        }
    }

    public function remove(string $location): void
    {
        try {
            $this->lib->deleteFile(Stuff::pathToArray($location));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotRemoveData($location), $ex->getCode(), $ex);
        }
    }
}
