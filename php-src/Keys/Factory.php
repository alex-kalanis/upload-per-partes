<?php

namespace kalanis\UploadPerPartes\Keys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Uploader\TargetSearch;


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

    /** @var array<int, string> */
    protected static $map = [
        self::VARIANT_VOLUME => SimpleVolume::class,
        self::VARIANT_RANDOM => Random::class,
        self::VARIANT_REDIS => Redis::class,
    ];

    /**
     * @param TargetSearch $target
     * @param int $variant
     * @param IUPPTranslations $lang
     * @throws UploadException
     * @return AKey
     */
    public static function getVariant(TargetSearch $target, int $variant, IUPPTranslations $lang): AKey
    {
        if (!isset(static::$map[$variant])) {
            throw new UploadException($lang->uppKeyVariantNotSet());
        }
        $class = static::$map[$variant];
        $lib = new $class($target, $lang);
        if (!$lib instanceof AKey) {
            throw new UploadException($lang->uppKeyVariantIsWrong($class));
        }
        return $lib;
    }
}
