<?php

namespace kalanis\UploadPerPartes\ServerData\KeyModifiers;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class EncodeFactory
 * @package kalanis\UploadPerPartes\ServerData\KeyModifiers
 * Select correct type of shared key
 */
class EncodeFactory
{
    use TLang;

    public const VARIANT_CLEAR = 1;
    public const VARIANT_RANDOM = 2;
    public const VARIANT_BASE64 = 3;
    public const VARIANT_MD5 = 4;
    public const VARIANT_HEX = 5;

    /** @var array<int, class-string<Interfaces\IEncodeForExternalExchange>> */
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
     * @param int|class-string<Interfaces\IEncodeForExternalExchange>|object|string $variant
     * @throws UploadException
     * @return Interfaces\IEncodeForExternalExchange
     */
    public function getVariant($variant): Interfaces\IEncodeForExternalExchange
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
     * @return Interfaces\IEncodeForExternalExchange
     */
    protected function checkObject(object $variant): Interfaces\IEncodeForExternalExchange
    {
        if ($variant instanceof Interfaces\IEncodeForExternalExchange) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return Interfaces\IEncodeForExternalExchange
     */
    protected function initDefined(string $variant): Interfaces\IEncodeForExternalExchange
    {
        try {
            /** @var class-string<Interfaces\IEncodeForExternalExchange> $variant */
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
