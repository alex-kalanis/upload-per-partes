<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class VolumeAdv
 * @package kalanis\UploadPerPartes\DataStorage
 * Processing info file on disk volume
 * Filesystem behaves oddly - beware of fucked up caching!
 * DO NOT TEST THIS WITH UNIT TESTS!
 */
class VolumeAdv extends VolumeBasic
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
        if (is_null($seek)) {  // append to end
            if (false === @file_put_contents($location, $content, FILE_APPEND)) {
                throw new UploadException($this->getUppLang()->uppCannotWriteFile($location));
            }
        } else { // append from position
            parent::addPart($location, $content, $seek); // do not write another seek func
        }
    }

    /**
     * @param string $location
     * @param int $offset
     * @param int<0, max>|null $limit
     * @throws UploadException
     * @return string
     * @codeCoverageIgnore
     */
    public function getPart(string $location, int $offset, ?int $limit = null): string
    {
        $data = @file_get_contents($location, false, null, $offset, $limit);
        if (false === $data) {
            throw new UploadException($this->getUppLang()->uppCannotReadFile($location));
        }
        return $data;
    }
}
