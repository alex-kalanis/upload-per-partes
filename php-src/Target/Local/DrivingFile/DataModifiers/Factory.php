<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\UploadException;
use ReflectionClass;
use ReflectionException;


/**
 * Class EncodeFactory
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\DataModifiers
 * Select correct type of pack form for passing data there and back
 */
class Factory
{
    use TLangInit;

    public const VARIANT_CLEAR = 1;
    public const VARIANT_BASE64 = 2;
    public const VARIANT_HEX = 3;

    /** @var array<string|int, class-string<AModifier>> */
    protected array $map = [
        self::VARIANT_CLEAR => Clear::class,
        self::VARIANT_BASE64 => Base64::class,
        self::VARIANT_HEX => Hex::class,
    ];

    /**
     * @param int|class-string<AModifier>|object|string|null $variant
     * @throws UploadException
     * @return AModifier
     */
    public function getDataModifier($variant): AModifier
    {
        if (is_object($variant)) {
            return $this->checkObject($variant);
        }
        if (isset($this->map[strval($variant)])) {
            return $this->initDefined($this->map[strval($variant)]);
        }
        if (is_string($variant)) {
            return $this->initDefined($variant);
        }
        throw new UploadException($this->getUppLang()->uppDataModifierVariantNotSet());
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return AModifier
     */
    protected function checkObject(object $variant): AModifier
    {
        if ($variant instanceof AModifier) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppDataModifierVariantIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return AModifier
     */
    protected function initDefined(string $variant): AModifier
    {
        try {
            /** @var class-string<AModifier> $variant */
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance($this->getUppLang()));
            }
            throw new UploadException($this->getUppLang()->uppDataModifierVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
