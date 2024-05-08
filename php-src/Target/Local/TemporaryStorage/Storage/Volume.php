<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage\Storage;


use kalanis\UploadPerPartes\Interfaces\ITemporaryStorage;
use kalanis\UploadPerPartes\Interfaces\IUppTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Volume
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage\Storage
 * Storing driving file data on file system volume
 */
class Volume implements ITemporaryStorage
{
    use TLang;

    protected string $pathPrefix = '';

    public function __construct(string $pathPrefix = '', IUppTranslations $lang = null)
    {
        $this->pathPrefix = $pathPrefix;
        $this->setUppLang($lang);
    }

    public function exists(string $path): bool
    {
        return @file_exists($this->fullPath($path));
    }

    public function readData(string $path, ?int $fromByte, ?int $length): string
    {
        if (PHP_VERSION_ID > 77000 || !is_null($length)) {
            $content = @file_get_contents($this->fullPath($path), false, null, intval($fromByte), $length);
        } else {
            $content = @file_get_contents($this->fullPath($path), false, null, intval($fromByte));
        }
        if (false === $content) {
            throw new UploadException($this->getUppLang()->uppCannotReadFile($path));
        }
        return $content;
    }

    public function truncate(string $path, int $fromByte): bool
    {
        $sourceStream = @fopen($this->fullPath($path), 'rb+');
        if (!$sourceStream) {
            throw new UploadException($this->getUppLang()->uppCannotWriteFile($path));
        }
        $a1 = @ftruncate($sourceStream, $fromByte);
        $a2 = @fclose($sourceStream);
        return $a1 && $a2;
    }

    public function append(string $path, string $content): bool
    {
        return false !== @file_put_contents($this->fullPath($path), $content, FILE_APPEND);
    }

    public function readStream(string $path)
    {
        $sourceStream = @fopen($this->fullPath($path), 'rb+');
        if (!$sourceStream) {
            throw new UploadException($this->getUppLang()->uppCannotReadFile($path));
        }
        return $sourceStream;
    }

    public function remove(string $key): bool
    {
        return @unlink($this->fullPath($key));
    }

    protected function fullPath(string $path): string
    {
        return $this->pathPrefix . $path;
    }
}
