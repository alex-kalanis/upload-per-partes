<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Uploader\Translations;


/**
 * Class AFormat
 * @package kalanis\UploadPerPartes\DriveFile
 * Drive file format - abstract for each variant
 */
abstract class AFormat
{
    const FORMAT_TEXT = 1;
    const FORMAT_JSON = 2;

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

    /**
     * @param Translations $lang
     * @param int $variant
     * @return AFormat
     * @throws UploadException
     */
    public static function getFormat(Translations $lang, int $variant): AFormat
    {
        switch ($variant) {
            case static::FORMAT_TEXT:
                return new Text();
            case static::FORMAT_JSON:
                return new Json();
            default:
                throw new UploadException($lang->driveFileVariantNotSet());
        }
    }
}
