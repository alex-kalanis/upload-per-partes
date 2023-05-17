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

    /**
     * @param string $location
     * @param string $content
     * @param int<0, max>|null $seek
     * @throws UploadException
     */
    public function addPart(string $location, string $content, ?int $seek = null): void
    {
        if (is_null($seek)) {  // append to end
            $pointer = @fopen($location, 'ab');
            if (false == $pointer) {
                throw new UploadException($this->getUppLang()->uppCannotOpenFile($location));
            }
            if (false === @fwrite($pointer, $content)) {
                // @codeCoverageIgnoreStart
                /** @scrutinizer ignore-unhandled */@fclose($pointer);
                throw new UploadException($this->getUppLang()->uppCannotWriteFile($location));
                // @codeCoverageIgnoreEnd
            }
            /** @scrutinizer ignore-unhandled */@fclose($pointer);
        } else { // append from position
            $pointer = @fopen($location, 'rb+');
            if (false === $pointer) {
                throw new UploadException($this->getUppLang()->uppCannotOpenFile($location));
            }
            // @codeCoverageIgnoreStart
            $position = @fseek($pointer, $seek);
            if (-1 == $position) {
                /** @scrutinizer ignore-unhandled */@fclose($pointer);
                throw new UploadException($this->getUppLang()->uppCannotSeekFile($location));
            }
            // @codeCoverageIgnoreEnd
            // @codeCoverageIgnoreStart
            if (false === @fwrite($pointer, $content)) {
                /** @scrutinizer ignore-unhandled */@fclose($pointer);
                throw new UploadException($this->getUppLang()->uppCannotWriteFile($location));
            }
            /** @scrutinizer ignore-unhandled */@fclose($pointer);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @param string $location
     * @param int $offset
     * @param int|null $limit
     * @throws UploadException
     * @return string
     */
    public function getPart(string $location, int $offset, ?int $limit = null): string
    {
        $pointer = @fopen($location, 'rb');
        if (false == $pointer) {
            // @codeCoverageIgnoreStart
            throw new UploadException($this->getUppLang()->uppCannotOpenFile($location));
        }
        // @codeCoverageIgnoreEnd
        if (empty($limit)) {
            $stat = @fseek($pointer, 0, SEEK_END);
            if (-1 == $stat) {
                // @codeCoverageIgnoreStart
                /** @scrutinizer ignore-unhandled */@fclose($pointer);
                throw new UploadException($this->getUppLang()->uppCannotSeekFile($location));
            }
            // @codeCoverageIgnoreEnd
            $limit = @ftell($pointer) - $offset;
        }
        // @codeCoverageIgnoreStart
        $position = @fseek($pointer, $offset, SEEK_SET);
        if (-1 == $position) {
            /** @scrutinizer ignore-unhandled */@fclose($pointer);
            throw new UploadException($this->getUppLang()->uppCannotSeekFile($location));
        }
        // @codeCoverageIgnoreEnd
        $data = @fread($pointer, intval($limit)); // @phpstan-ignore-line

        if (false === $data) {
            // @codeCoverageIgnoreStart
            throw new UploadException($this->getUppLang()->uppCannotReadFile($location));
            // @codeCoverageIgnoreEnd
        }
        return $data;
    }

    /**
     * @param string $location
     * @param int $offset
     * @throws UploadException
     */
    public function truncate(string $location, int $offset): void
    {
        $pointer = @fopen($location, 'rb+');
        if (false !== $pointer) {
            $stat = @rewind($pointer);
            if (false === $stat) {
                // @codeCoverageIgnoreStart
                /** @scrutinizer ignore-unhandled */@fclose($pointer);
                throw new UploadException($this->getUppLang()->uppCannotTruncateFile($location));
            }
            // @codeCoverageIgnoreEnd
            if (!ftruncate($pointer, $offset)) { // @phpstan-ignore-line
                // @codeCoverageIgnoreStart
                /** @scrutinizer ignore-unhandled */@fclose($pointer);
                throw new UploadException($this->getUppLang()->uppCannotTruncateFile($location));
            }
            // @codeCoverageIgnoreEnd
            /** @scrutinizer ignore-unhandled */@rewind($pointer);
            /** @scrutinizer ignore-unhandled */@fclose($pointer);
        }
    }

    public function remove(string $location): void
    {
        if (!@unlink($location)) {
            throw new UploadException($this->getUppLang()->uppCannotRemoveData($location));
        }
    }
}
