<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage\Storage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\UploadPerPartes\Interfaces\ITemporaryStorage;
use kalanis\UploadPerPartes\Interfaces\IUppTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Files
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage\Storage
 * Storing driving file data on files system - based on kw_files
 */
class Files implements ITemporaryStorage
{
    use TLang;

    /** @var string[] */
    protected array $targetDir = [];
    protected CompositeAdapter $files;
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
        $this->setUppLang($lang);
    }

    public function exists(string $path): bool
    {
        try {
            return $this->files->exists($this->fullPath($path));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readData(string $path, ?int $fromByte, ?int $length): string
    {
        try {
            return $this->files->readFile($this->fullPath($path), $fromByte, $length);
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function truncate(string $path, int $fromByte): bool
    {
        try {
            $nameFull = $this->fullPath($path);
            $tempFull = $this->fullPath($path . '.TEMPORARY_FILE');
            $a = $this->files->moveFile($nameFull, $tempFull);
            $old = $this->files->readFileStream($tempFull);
            $b = rewind($old);
            $new = fopen('php://temp', 'rb+');
            if (!is_resource($new)) {
                // @codeCoverageIgnoreStart
                // phpstan
                throw new UploadException($this->getUppLang()->uppCannotReadFile('temp'));
            }
            // @codeCoverageIgnoreEnd
            $c = boolval(stream_copy_to_stream($old, $new, $fromByte));
            rewind($new);
            $d = $this->files->saveFileStream($nameFull, $new);
            $e = $this->files->deleteFile($tempFull);
            return $a && $b && $c && $d && $e;
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function append(string $path, string $content): bool
    {
        try {
            return $this->files->saveFile($this->fullPath($path), $content, null, FILE_APPEND);
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readStream(string $path)
    {
        try {
            return $this->files->readFileStream($this->fullPath($path));
        } catch (FilesException | PathsException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function remove(string $path): bool
    {
        try {
            return $this->files->deleteFile($this->fullPath($path));
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
}
