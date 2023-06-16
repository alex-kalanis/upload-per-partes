<?php

namespace kalanis\UploadPerPartes\Keys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Keys
 * Select correct type of shared key
 */
class Factory
{
    use TLang;

    const VARIANT_VOLUME = 1;
    const VARIANT_RANDOM = 2;
    const VARIANT_REDIS = 3;

    /** @var array<int, class-string<AKey>> */
    protected static $map = [
        self::VARIANT_VOLUME => SimpleVolume::class,
        self::VARIANT_RANDOM => Random::class,
        self::VARIANT_REDIS => Redis::class,
    ];

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param TargetSearch $target
     * @param int $variant
     * @throws UploadException
     * @return AKey
     */
    public function getVariant(TargetSearch $target, int $variant): AKey
    {
        if (!isset(static::$map[$variant])) {
            throw new UploadException($this->getUppLang()->uppKeyVariantNotSet());
        }
        $class = static::$map[$variant];
        try {
            $ref = new ReflectionClass($class);
            if ($ref->isInstantiable()) {
                $lib = $ref->newInstance($target, $this->getUppLang());
                if ($lib instanceof AKey) {
                    return $lib;
                }
            }
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong($class));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
