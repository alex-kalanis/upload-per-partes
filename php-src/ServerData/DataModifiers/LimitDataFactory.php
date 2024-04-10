<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class LimitDataFactory
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Select what will be used as data base to shared key
 */
class LimitDataFactory
{
    use TLang;

    public const VARIANT_NAME = 1;
    public const VARIANT_FULL_PATH = 2;
    public const VARIANT_SALTED_NAME = 3;
    public const VARIANT_SALTED_FULL = 4;
    public const VARIANT_SERIALIZE = 5;
    public const VARIANT_JSON = 6;

    /** @var array<int, class-string<Interfaces\ILimitDataInternalKey>> */
    protected array $map = [
        self::VARIANT_NAME => Name::class,
        self::VARIANT_FULL_PATH => FullPath::class,
        self::VARIANT_SALTED_NAME => SaltedName::class,
        self::VARIANT_SALTED_FULL => SaltedFullPath::class,
        self::VARIANT_SERIALIZE => Serialize::class,
        self::VARIANT_JSON => Json::class,
    ];

    public function __construct(?Interfaces\IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param int|class-string<Interfaces\ILimitDataInternalKey>|object|string $variant
     * @throws UploadException
     * @return Interfaces\ILimitDataInternalKey
     */
    public function getVariant($variant): Interfaces\ILimitDataInternalKey
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
        throw new UploadException($this->getUppLang()->uppKeyModifierNotSet());
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return Interfaces\ILimitDataInternalKey
     */
    protected function checkObject(object $variant): Interfaces\ILimitDataInternalKey
    {
        if ($variant instanceof Interfaces\ILimitDataInternalKey) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return Interfaces\ILimitDataInternalKey
     */
    protected function initDefined(string $variant): Interfaces\ILimitDataInternalKey
    {
        try {
            /** @var class-string<Interfaces\ILimitDataInternalKey> $variant */
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance($this->getUppLang()));
            }
            throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
