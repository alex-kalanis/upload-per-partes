<?php

namespace kalanis\UploadPerPartes\ServerData\KeyModifiers;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class GenerateFactory
 * @package kalanis\UploadPerPartes\ServerData\KeyModifiers
 * Select correct type of shared key
 */
class GenerateFactory
{
    use TLang;

    const VARIANT_CLEAR = 1;
    const VARIANT_RANDOM = 2;
    const VARIANT_BASE64 = 3;
    const VARIANT_MD5 = 4;
    const VARIANT_HEX = 5;

    /** @var array<int, class-string<Interfaces\IStorageKey>> */
    protected $map = [
        self::VARIANT_CLEAR => Clear::class,
        self::VARIANT_RANDOM => Random::class,
        self::VARIANT_BASE64 => Base64::class,
        self::VARIANT_MD5 => Md5::class,
        self::VARIANT_HEX => Hex::class,
    ];

    public function __construct(?Interfaces\IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param int|class-string<Interfaces\IStorageKey>|object|string $variant
     * @throws UploadException
     * @return Interfaces\IStorageKey
     */
    public function getVariant($variant): Interfaces\IStorageKey
    {
        if (is_object($variant)) {
            return $this->checkObject($variant);
        }
        if (isset($this->map[$variant])) {
            return $this->initDefined($this->map[$variant]);
        }
        if (is_string($variant)) {
            return $this->initDefined($variant);
        }
        throw new UploadException($this->getUppLang()->uppKeyVariantNotSet());
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return Interfaces\IStorageKey
     */
    protected function checkObject(object $variant): Interfaces\IStorageKey
    {
        if ($variant instanceof Interfaces\IStorageKey) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($variant)));
    }

    /**
     * @param class-string<Interfaces\IStorageKey>|string $variant
     * @throws UploadException
     * @return Interfaces\IStorageKey
     */
    protected function initDefined(string $variant): Interfaces\IStorageKey
    {
        try {
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance($this->getUppLang()));
            }
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
