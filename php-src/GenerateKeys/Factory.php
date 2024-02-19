<?php

namespace kalanis\UploadPerPartes\GenerateKeys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\GenerateKeys
 * Select correct type of shared key
 */
class Factory
{
    use TLang;

    const VARIANT_CLEAR = 1;
    const VARIANT_RANDOM = 2;
    const VARIANT_BASE64 = 3;
    const VARIANT_MD5 = 4;

    /** @var array<int, class-string<AKey>> */
    protected $map = [
        self::VARIANT_CLEAR => Clear::class,
        self::VARIANT_RANDOM => Random::class,
        self::VARIANT_BASE64 => Base64::class,
        self::VARIANT_MD5 => Md5::class,
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
                $lib = $ref->newInstance($this->getUppLang());
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
