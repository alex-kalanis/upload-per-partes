<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Uploader\Translations;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\DriveFile
 * Drive file format - Factory to get formats
 */
class Factory
{
    const FORMAT_TEXT = 1;
    const FORMAT_JSON = 2;

    protected static $map = [
        self::FORMAT_TEXT => '\kalanis\UploadPerPartes\InfoFormat\Text',
        self::FORMAT_JSON => '\kalanis\UploadPerPartes\InfoFormat\Json',
    ];

    /**
     * @param Translations $lang
     * @param int $variant
     * @return AFormat
     * @throws UploadException
     */
    public static function getFormat(Translations $lang, int $variant): AFormat
    {
        if (!isset(static::$map[$variant])) {
            throw new UploadException($lang->driveFileVariantNotSet());
        }
        $class = static::$map[$variant];
        return new $class();
    }
}
