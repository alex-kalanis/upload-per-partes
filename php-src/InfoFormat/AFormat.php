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
     * @param mixed $content
     * @return Data
     * @throws UploadException
     */
    abstract public function fromFormat(string $content): Data;

    /**
     * @param Data $data
     * @return string
     * @throws UploadException
     */
    abstract public function toFormat(Data $data): string;
}
