<?php

namespace UploadPerPartes\DataStorage;

use UploadPerPartes\Exceptions\UploadException;

/**
 * Class Volume
 * @package UploadPerPartes\DataStorage
 * Processing info file on disk volume
 */
class Volume extends AStorage
{
    public function addPart(string $location, string $content, ?int $seek = null): void
    {
        if (is_numeric($seek)) {
            $pointer = fopen($location, 'wb');
            if (false === $pointer) {
                throw new UploadException($this->lang->cannotOpenFile());
            }
            $position = fseek($pointer, $seek);
            if ($position == -1) {
                throw new UploadException($this->lang->cannotSeekFile());
            }
            if (false === fwrite($pointer, $content, strlen($content))) {
                throw new UploadException($this->lang->cannotWriteFile());
            }
            fclose($pointer);
        } else {
            $pointer = fopen($location, 'ab');
            if (false == $pointer) {
                throw new UploadException($this->lang->cannotOpenFile());
            }
            if (false === fwrite($pointer, $content, strlen($content))) {
                throw new UploadException($this->lang->cannotWriteFile());
            }
            fclose($pointer);
        }
    }

    public function getPart(string $location, int $offset, int $limit): string
    {
        $data = file_get_contents(
            $location,
            false,
            null,
            $offset,
            $limit
        );
        if (false === $data) {
            throw new UploadException($this->lang->cannotReadFile());
        }
        return $data;
    }

    public function truncate(string $location, int $offset): void
    {
        $handle = fopen($location, 'r+');
        if (!ftruncate($handle, $offset)) {
            fclose($handle);
            throw new UploadException($this->lang->cannotTruncateFile());
        }
        rewind($handle);
        fclose($handle);
    }

    public function remove(string $location): void
    {
        if (!@unlink($location)) {
            throw new UploadException($this->lang->cannotRemoveData());
        }
    }
}