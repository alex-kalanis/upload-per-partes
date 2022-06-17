<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class AFormat
 * @package kalanis\UploadPerPartes\DriveFile
 * Drive file format - abstract for each variant
 */
abstract class AFormat
{
    /**
     * @param string $content
     * @throws UploadException
     * @return Data
     */
    abstract public function fromFormat(string $content): Data;

    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    abstract public function toFormat(Data $data): string;
}
