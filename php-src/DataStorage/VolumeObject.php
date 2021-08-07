<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
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
     * @param int|null $seek
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function addPart(string $location, string $content, ?int $seek = null): void
    {
        $file = new SplFileObject($location, 'r+');
        if (!$file) {
            throw new UploadException($this->lang->cannotOpenFile());
        }
        $position = is_null($seek) ? @$file->fseek(0, SEEK_END) : @$file->fseek($seek) ;
        if ($position == -1) {
            unset($file);
            throw new UploadException($this->lang->cannotSeekFile());
        }
        if (false === @$file->fwrite($content)) {
            unset($file);
            throw new UploadException($this->lang->cannotWriteFile());
        }
        unset($file);
    }

    /**
     * @param string $location
     * @param int $offset
     * @param int|null $limit
     * @return string
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function getPart(string $location, int $offset, ?int $limit = null): string
    {
        $file = new SplFileObject($location, 'r+');
        if (!$file) {
            throw new UploadException($this->lang->cannotOpenFile());
        }
        if (empty($limit)) {
            @$file->fseek(0, SEEK_END);
            $limit = @$file->ftell() - $offset;
        }
        $position = @$file->fseek($offset, SEEK_SET);
        if ($position == -1) {
            unset($file);
            throw new UploadException($this->lang->cannotSeekFile());
        }
        $data = @$file->fread((int)$limit);

        if (false === $data) {
            unset($file);
            throw new UploadException($this->lang->cannotReadFile());
        }
        unset($file);
        return $data;
    }

    /**
     * @param string $location
     * @param int $offset
     * @throws UploadException
     * @codeCoverageIgnore
     */
    public function truncate(string $location, int $offset): void
    {
        $file = new SplFileObject($location, 'r+');
        $file->rewind();
        if (!$file->ftruncate($offset)) {
            unset($file);
            throw new UploadException($this->lang->cannotTruncateFile());
        }
        @$file->rewind();
        unset($file);
    }
}
