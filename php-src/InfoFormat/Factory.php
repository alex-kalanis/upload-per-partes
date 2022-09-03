<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IInfoFormatting;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\DriveFile
 * Drive file format - Factory to get formats
 */
class Factory
{
    const FORMAT_TEXT = 1;
    const FORMAT_JSON = 2;

    /** @var array<int, string> */
    protected static $map = [
        self::FORMAT_TEXT => '\kalanis\UploadPerPartes\InfoFormat\Text',
        self::FORMAT_JSON => '\kalanis\UploadPerPartes\InfoFormat\Json',
    ];

    /**
     * @param IUPPTranslations $lang
     * @param int $variant
     * @throws UploadException
     * @return IInfoFormatting
     */
    public static function getFormat(IUPPTranslations $lang, int $variant): IInfoFormatting
    {
        if (!isset(static::$map[$variant])) {
            throw new UploadException($lang->uppDriveFileVariantNotSet());
        }
        $class = static::$map[$variant];
        $lib = new $class();
        if (!$lib instanceof IInfoFormatting) {
            throw new UploadException($lang->uppDriveFileVariantIsWrong($class));
        }
        return $lib;
    }
}
