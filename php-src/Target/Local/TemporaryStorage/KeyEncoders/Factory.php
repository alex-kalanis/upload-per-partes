<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders
 * Factory to get file name on temporary storage
 */
class Factory
{
    use TLangInit;

    public const FORMAT_NAME = '1';
    public const FORMAT_FULL = '2';
    public const FORMAT_SALTED_NAME = '3';
    public const FORMAT_SALTED_FULL = '4';
    public const FORMAT_RANDOM = '5';

    /** @var array<int|string, class-string<AEncoder>> */
    protected array $map = [
        self::FORMAT_NAME => Name::class,
        self::FORMAT_FULL => FullPath::class,
        self::FORMAT_SALTED_NAME => SaltedName::class,
        self::FORMAT_SALTED_FULL => SaltedFullPath::class,
        self::FORMAT_RANDOM => Random::class,
    ];

    /**
     * @param Config $config
     * @throws UploadException
     * @return AEncoder
     */
    public function getKeyEncoder(Config $config): AEncoder
    {
        if (is_object($config->temporaryEncoder)) {
            return $this->checkObject($config->temporaryEncoder);
        }
        if (isset($this->map[strval($config->temporaryEncoder)])) {
            return $this->initDefined($this->map[strval($config->temporaryEncoder)]);
        }
        if (is_string($config->temporaryEncoder)) {
            return $this->initDefined($config->temporaryEncoder);
        }
        throw new UploadException($this->getUppLang()->uppTempEncoderVariantNotSet());
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
        throw new UploadException($this->getUppLang()->uppTempEncoderVariantIsWrong(get_class($variant)));
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
            throw new UploadException($this->getUppLang()->uppTempEncoderVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
