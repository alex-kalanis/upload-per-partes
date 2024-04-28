<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders
 * Drive file format - Factory to get formats
 *
 * How to pack and unpack data in info file on server
 */
class Factory
{
    use TLangInit;

    public const FORMAT_TEXT = '1';
    public const FORMAT_JSON = '2';
    public const FORMAT_LINE = '3';
    public const FORMAT_SERIAL = '4';

    /** @var array<string|int, class-string<AEncoder>> */
    protected array $map = [
        self::FORMAT_TEXT => Text::class,
        self::FORMAT_JSON => Json::class,
        self::FORMAT_LINE => Line::class,
        self::FORMAT_SERIAL => Serialize::class,
    ];

    /**
     * @param Config $config
     * @throws UploadException
     * @return AEncoder
     */
    public function getDataEncoder(Config $config): AEncoder
    {
        if (is_object($config->dataEncoder)) {
            return $this->checkObject($config->dataEncoder);
        }
        if (isset($this->map[$config->dataEncoder])) {
            return $this->initDefined($this->map[$config->dataEncoder]);
        }
        if (is_string($config->dataEncoder)) {
            return $this->initDefined($config->dataEncoder);
        }
        throw new UploadException($this->getUppLang()->uppDataEncoderVariantNotSet());
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
        throw new UploadException($this->getUppLang()->uppDataEncoderVariantIsWrong(get_class($variant)));
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
            throw new UploadException($this->getUppLang()->uppDataEncoderVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
