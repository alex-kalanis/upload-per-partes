<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class VolumeBasic
 * @package kalanis\UploadPerPartes\DataStorage
 * Processing info file on disk volume
 * Filesystem behaves oddly - beware of fucked up caching!
 * When someone got idea how to test it without ignoring failed states, please tell me.
 */
class VolumeBasic extends AStorage
{
    public function exists(string $location): bool
    {
        return is_file($location);
    }

    public function addPart(string $location, string $content, ?int $seek = null): void
    {
        if (is_null($seek)) {  // append to end
            $pointer = @fopen($location, 'a');
            if (false == $pointer) {
                throw new UploadException($this->lang->uppCannotOpenFile($location));
            }
            if (false === @fwrite($pointer, $content)) {
                // @codeCoverageIgnoreStart
                @fclose($pointer);
                throw new UploadException($this->lang->uppCannotWriteFile($location));
                // @codeCoverageIgnoreEnd
            }
            @fclose($pointer);
        } else { // append from position
            $pointer = @fopen($location, 'r+');
            if (false === $pointer) {
                throw new UploadException($this->lang->uppCannotOpenFile($location));
            }
            // @codeCoverageIgnoreStart
            $position = @fseek($pointer, $seek);
            if ($position == -1) {
                @fclose($pointer);
                throw new UploadException($this->lang->uppCannotSeekFile($location));
            }
            // @codeCoverageIgnoreEnd
            // @codeCoverageIgnoreStart
            if (false === @fwrite($pointer, $content)) {
                @fclose($pointer);
                throw new UploadException($this->lang->uppCannotWriteFile($location));
            }
            @fclose($pointer);
            // @codeCoverageIgnoreEnd
        }
    }

    public function getPart(string $location, int $offset, ?int $limit = null): string
    {
        $pointer = @fopen($location, 'r');
        if (false == $pointer) {
            // @codeCoverageIgnoreStart
            throw new UploadException($this->lang->uppCannotOpenFile($location));
            // @codeCoverageIgnoreEnd
        }
        if (empty($limit)) {
            @fseek($pointer, 0, SEEK_END);
            $limit = @ftell($pointer) - $offset;
        }
        // @codeCoverageIgnoreStart
        $position = @fseek($pointer, $offset, SEEK_SET);
        if ($position == -1) {
            @fclose($pointer);
            throw new UploadException($this->lang->uppCannotSeekFile($location));
        }
        // @codeCoverageIgnoreEnd
        $data = @fread($pointer, (int)$limit);

        if (false === $data) {
            // @codeCoverageIgnoreStart
            throw new UploadException($this->lang->uppCannotReadFile($location));
            // @codeCoverageIgnoreEnd
        }
        return $data;
    }

    public function truncate(string $location, int $offset): void
    {
        $pointer = @fopen($location, 'r+');
        @rewind($pointer);
        if (!ftruncate($pointer, $offset)) {
            // @codeCoverageIgnoreStart
            @fclose($pointer);
            throw new UploadException($this->lang->uppCannotTruncateFile($location));
            // @codeCoverageIgnoreEnd
        }
        @rewind($pointer);
        @fclose($pointer);
    }

    public function remove(string $location): void
    {
        if (!@unlink($location)) {
            throw new UploadException($this->lang->uppCannotRemoveData($location));
        }
    }
}
