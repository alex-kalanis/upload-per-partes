<?php

namespace UploadPerPartes\Storage;

use UploadPerPartes\Exceptions\UploadException;

/**
 * Class AFormat
 * @package UploadPerPartes\DriveFile
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
            throw new UploadException($this->lang->driveFileCannotRead());
        }
        return $content;
    }

    public function save(string $key, string $data): void
    {
        if (false === @file_put_contents($key, $data)) {
            throw new UploadException($this->lang->driveFileCannotWrite());
        }
    }

    public function remove(string $key): void
    {
        if (!@unlink($key)) {
            throw new UploadException($this->lang->driveFileCannotRemove());
        }
    }
}