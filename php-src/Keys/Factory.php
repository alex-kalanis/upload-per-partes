<?php

namespace kalanis\UploadPerPartes\Keys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use kalanis\UploadPerPartes\Uploader\Translations;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Keys
 * Select correct type of shared key
 */
class Factory
{
    const VARIANT_VOLUME = 1;
    const VARIANT_RANDOM = 2;
    const VARIANT_REDIS = 3;

    protected static $map = [
        self::VARIANT_VOLUME => '\kalanis\UploadPerPartes\Keys\SimpleVolume',
        self::VARIANT_RANDOM => '\kalanis\UploadPerPartes\Keys\Random',
        self::VARIANT_REDIS => '\kalanis\UploadPerPartes\Keys\Redis',
    ];

    /**
     * @param Translations $lang
     * @param TargetSearch $target
     * @param int $variant
     * @return AKey
     * @throws UploadException
     */
    public static function getVariant(Translations $lang, TargetSearch $target, int $variant): AKey
    {
        if (!isset(static::$map[$variant])) {
            throw new UploadException($lang->keyVariantNotSet());
        }
        $class = static::$map[$variant];
        return new $class($lang, $target);
    }
}
