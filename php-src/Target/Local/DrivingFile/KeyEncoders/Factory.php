<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\KeyEncoders
 * Select what will be used as data base to shared key
 */
class Factory
{
    use TLangInit;

    public const VARIANT_NAME = '1';
    public const VARIANT_FULL_PATH = '2';
    public const VARIANT_SALTED_NAME = '3';
    public const VARIANT_SALTED_FULL = '4';
    public const VARIANT_SERIALIZE = '5';
    public const VARIANT_JSON = '6';

    /** @var array<string|int, class-string<AEncoder>> */
    protected array $map = [
        self::VARIANT_NAME => Name::class,
        self::VARIANT_FULL_PATH => FullPath::class,
        self::VARIANT_SALTED_NAME => SaltedName::class,
        self::VARIANT_SALTED_FULL => SaltedFullPath::class,
        self::VARIANT_SERIALIZE => Serialize::class,
        self::VARIANT_JSON => Json::class,
    ];

    /**
     * @param Config $config
     * @throws UploadException
     * @return AEncoder
     */
    public function getKeyEncoder(Config $config): AEncoder
    {
        if (is_object($config->keyEncoder)) {
            return $this->checkObject($config->keyEncoder);
        }
        if (isset($this->map[$config->keyEncoder])) {
            return $this->initDefined($this->map[$config->keyEncoder]);
        }
        if (is_string($config->keyEncoder)) {
            return $this->initDefined($config->keyEncoder);
        }
        throw new UploadException($this->getUppLang()->uppKeyEncoderVariantNotSet());
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return AEncoder
     */
    protected function checkObject(object $variant): AEncoder
    {
        if ($variant instanceof AEncoder) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppKeyEncoderVariantIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return AEncoder
     */
    protected function initDefined(string $variant): AEncoder
    {
        try {
            /** @var class-string<AEncoder> $variant */
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance($this->getUppLang()));
            }
            throw new UploadException($this->getUppLang()->uppKeyEncoderVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
