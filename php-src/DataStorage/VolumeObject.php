<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use RuntimeException;
use SplFileObject;


/**
 * Class VolumeBasic
 * @package kalanis\UploadPerPartes\DataStorage
 * Processing info file on disk volume
 * Filesystem behaves oddly - beware of fucked up caching!
 * When someone got idea how to test it without ignoring failed states, please tell me.
 * DO NOT TEST THIS WITH UNIT TESTS!
 */
class VolumeObject extends VolumeBasic
{
    /**
     * @param string $location
     * @param string $content
     * @param int<0, max>|null $seek
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function addPart(string $location, string $content, ?int $seek = null): void
    {
        $file = new SplFileObject($location, 'rb+');
        if (false === @$file->ftell()) {
            throw new UploadException($this->getUppLang()->uppCannotOpenFile($location));
        }
        $position = is_null($seek) ? @$file->fseek(0, SEEK_END) : @$file->fseek($seek) ;
        if (-1 == $position) {
            unset($file);
            throw new UploadException($this->getUppLang()->uppCannotSeekFile($location));
        }
        $status = @$file->fwrite($content);
        if (false === $status || is_null($status)) { /** @phpstan-ignore-line probably bug in phpstan definitions */
            unset($file);
            throw new UploadException($this->getUppLang()->uppCannotWriteFile($location));
        }
        unset($file);
    }

    /**
     * @param string $location
     * @param int<0, max> $offset
     * @param int<0, max>|null $limit
     * @throws UploadException
     * @return string
     * @codeCoverageIgnore
     */
    public function getPart(string $location, int $offset, ?int $limit = null): string
    {
        try {
            $file = new SplFileObject($location, 'rb+');
            if (false === @$file->ftell()) {
                throw new UploadException($this->getUppLang()->uppCannotOpenFile($location));
            }
            if (empty($limit)) {
                $position = @$file->fseek(0, SEEK_END);
                if (-1 == $position) {
                    unset($file);
                    throw new UploadException($this->getUppLang()->uppCannotSeekFile($location));
                }
                $limit = @$file->ftell() - $offset;
            }
            $position = @$file->fseek($offset, SEEK_SET);
            if (-1 == $position) {
                unset($file);
                throw new UploadException($this->getUppLang()->uppCannotSeekFile($location));
            }
            $data = @$file->fread(intval($limit));

            if (false === $data) {
                unset($file);
                throw new UploadException($this->getUppLang()->uppCannotReadFile($location));
            }
            unset($file);
            return $data;
        } catch (RuntimeException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotReadFile($location), 0, $ex);
        }
    }

    /**
     * @param string $location
     * @param int<0, max> $offset
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function truncate(string $location, int $offset): void
    {
        try {
            $file = new SplFileObject($location, 'rb+');
            $file->rewind();
            if (!$file->ftruncate($offset)) {
                unset($file);
                throw new UploadException($this->getUppLang()->uppCannotTruncateFile($location));
            }
            $file->rewind();
            unset($file);
        } catch (RuntimeException $ex) {
            throw new UploadException($this->getUppLang()->uppCannotTruncateFile($location), 0, $ex);
        }
    }
}
