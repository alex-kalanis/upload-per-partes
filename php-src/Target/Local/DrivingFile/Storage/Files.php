<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Files
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage
 * Storing driving file data in files system - based on kw_files
 */
class Files implements Interfaces\IDrivingFile
{
    use TLang;

    /** @var string[] */
    protected array $targetDir;
    protected CompositeAdapter $files;
    protected ArrayPath $paths;

    /**
     * @param CompositeAdapter $files
     * @param string[] $targetDir
     * @param ArrayPath|null $arrayPaths
     * @param Interfaces\IUppTranslations|null $lang
     */
    public function __construct(CompositeAdapter $files, array $targetDir = [], ?ArrayPath $arrayPaths = null, ?Interfaces\IUppTranslations $lang = null)
    {
        $this->files = $files;
        $this->targetDir = $targetDir;
        $this->paths = $arrayPaths ?: new ArrayPath();
        $this->setUppLang($lang);
    }

    public function exists(string $key): bool
    {
        try {
            return $this->files->exists($this->fullPath($key));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function store(string $key, string $data): string
    {
        try {
            if (!$this->files->saveFile($this->fullPath($key), $data)) {
                throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
            }
            return $key;
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function get(string $key): string
    {
        try {
            return $this->files->readFile($this->fullPath($key));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function remove(string $key): bool
    {
        try {
            return $this->files->deleteFile($this->fullPath($key));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $key
     * @throws PathsException
     * @return string[]
     */
    protected function fullPath(string $key): array
    {
        return array_merge($this->targetDir, $this->paths->setString($key)->getArray());
    }

    public function checkKeyEncoder(DrivingFile\KeyEncoders\AEncoder $encoder): bool
    {
        if (!$encoder instanceof Interfaces\Storages\ForFiles) {
            throw new UploadException($this->getUppLang()->uppKeyEncoderVariantIsWrong(get_class($encoder)));
        }
        return true;
    }
}
