<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\Storage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Extended\FindFreeName;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\UploadPerPartes\Interfaces\IFinalStorage;
use kalanis\UploadPerPartes\Interfaces\IUppTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Files
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\Storage
 * Where to store data on target destination - storage based on kw_files
 */
class Files implements IFinalStorage
{
    use TLang;

    /** @var string[] */
    protected array $targetDir = [];
    protected CompositeAdapter $files;
    protected FindFreeName $freeName;
    protected ArrayPath $paths;

    /**
     * @param CompositeAdapter $files
     * @param string[] $targetDir
     * @param ArrayPath|null $arrayPaths
     * @param IUppTranslations|null $lang
     */
    public function __construct(CompositeAdapter $files, array $targetDir = [], ?ArrayPath $arrayPaths = null, IUppTranslations $lang = null)
    {
        $this->files = $files;
        $this->targetDir = $targetDir;
        $this->paths = $arrayPaths ?: new ArrayPath();
        $this->freeName = new FindFreeName($files);
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

    public function store(string $key, $data): bool
    {
        try {
            return $this->files->saveFileStream($this->fullPath($key), $data);
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotWriteFile($key), $ex->getCode(), $ex);
        }
    }

    public function findName(string $key): string
    {
        try {
            $this->paths->setString($key);
            $fileName = $this->paths->getFileName();
            $directory = $this->paths->getArrayDirectory();
            $freeName = $this->freeName->findFreeName(
                array_merge($this->targetDir, $directory),
                Stuff::fileBase($fileName),
                '.' . Stuff::fileExt($fileName)
            );
            return $this->paths->setArray(array_merge(
                $directory,
                [$freeName]
            ))->getString();
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotWriteFile($key), $ex->getCode(), $ex);
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
}
