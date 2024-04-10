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

    public const VARIANT_CLEAR = 1;
    public const VARIANT_RANDOM = 2;
    public const VARIANT_BASE64 = 3;
    public const VARIANT_MD5 = 4;
    public const VARIANT_HEX = 5;

    /** @var array<int, class-string<Interfaces\IEncodeForInternalStorage>> */
    protected array $map = [
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
     * @param int|class-string<Interfaces\IEncodeForInternalStorage>|object|string $variant
     * @throws UploadException
     * @return Interfaces\IEncodeForInternalStorage
     */
    public function getVariant($variant): Interfaces\IEncodeForInternalStorage
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
     * @return Interfaces\IEncodeForInternalStorage
     */
    protected function checkObject(object $variant): Interfaces\IEncodeForInternalStorage
    {
        if ($variant instanceof Interfaces\IEncodeForInternalStorage) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return Interfaces\IEncodeForInternalStorage
     */
    protected function initDefined(string $variant): Interfaces\IEncodeForInternalStorage
    {
        try {
            /** @var class-string<Interfaces\IEncodeForInternalStorage> $variant */
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
