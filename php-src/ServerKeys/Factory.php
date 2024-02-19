<?php

namespace kalanis\UploadPerPartes\ServerKeys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\ServerKeys
 * Select correct type of shared key
 */
class Factory
{
    use TLang;

    const VARIANT_VOLUME = 1;
    const VARIANT_REDIS = 2;

    /** @var array<int, class-string<AKey>> */
    protected $map = [
        self::VARIANT_VOLUME => Volume::class,
        self::VARIANT_REDIS => Redis::class,
    ];

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param int $variant
     * @throws UploadException
     * @return AKey
     */
    public function getVariant(int $variant): AKey
    {
        if (!isset($this->map[$variant])) {
            throw new UploadException($this->getUppLang()->uppKeyVariantNotSet());
        }
        $class = $this->map[$variant];
        try {
            $ref = new ReflectionClass($class);
            if ($ref->isInstantiable()) {
                $lib = $ref->newInstance();
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
