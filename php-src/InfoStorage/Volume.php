<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class Volume
 * @package kalanis\UploadPerPartes\InfoStorage
 * Processing info file on disk volume
 */
class Volume extends AStorage
{
    public function exists(string $key): bool
    {
        return is_file($key);
    }

    public function load(string $key): string
    {
        $content = @file_get_contents($key);
        if (false === $content) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRead($key));
        }
        return $content;
    }

    public function save(string $key, string $data): void
    {
        if (false === @file_put_contents($key, $data)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
        }
    }

    public function remove(string $key): void
    {
        if (!@unlink($key)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key));
        }
    }
}
