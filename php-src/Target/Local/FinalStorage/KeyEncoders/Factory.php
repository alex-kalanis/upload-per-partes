<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders
 * Factory to get final file name on target storage
 */
class Factory
{
    use TLangInit;

    public const FORMAT_NAME = '1';
    public const FORMAT_FULL = '2';
    public const FORMAT_SALTED_NAME = '3';
    public const FORMAT_SALTED_FULL = '4';

    /** @var array<int|string, class-string<AEncoder>> */
    protected array $map = [
        self::FORMAT_NAME => Name::class,
        self::FORMAT_FULL => FullPath::class,
        self::FORMAT_SALTED_NAME => SaltedName::class,
        self::FORMAT_SALTED_FULL => SaltedFullPath::class,
    ];

    /**
     * @param Config $config
     * @throws UploadException
     * @return AEncoder
     */
    public function getKeyEncoder(Config $config): AEncoder
    {
        if (is_object($config->finalEncoder)) {
            return $this->checkObject($config->finalEncoder);
        }
        if (isset($this->map[strval($config->finalEncoder)])) {
            return $this->initDefined($this->map[strval($config->finalEncoder)]);
        }
        if (is_string($config->finalEncoder)) {
            return $this->initDefined($config->finalEncoder);
        }
        throw new UploadException($this->getUppLang()->uppFinalEncoderVariantNotSet());
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
        throw new UploadException($this->getUppLang()->uppFinalEncoderVariantIsWrong(get_class($variant)));
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
            throw new UploadException($this->getUppLang()->uppFinalEncoderVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
