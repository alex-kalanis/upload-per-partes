<?php

namespace kalanis\UploadPerPartes\Target\Local\ContentDecoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\ContentDecoders
 * Which decoder will be used for reconstruction of data passed from client
 */
class Factory
{
    use TLangInit;

    public const FORMAT_BASE64 = 'base64';
    public const FORMAT_HEX = 'hex';
    public const FORMAT_RAW = 'raw';

    /** @var array<string|int, class-string<Interfaces\IContentDecoder>> */
    protected array $map = [
        self::FORMAT_BASE64 => Base64::class,
        self::FORMAT_HEX => Hex::class,
        self::FORMAT_RAW => Raw::class,
    ];

    /**
     * @param string $method
     * @throws UploadException
     * @return Interfaces\IContentDecoder
     */
    public function getDecoder(string $method): Interfaces\IContentDecoder
    {
        $variant = empty($method) ? self::FORMAT_BASE64 : $method;
        if (isset($this->map[strval($variant)])) {
            return $this->initDefined($this->map[strval($variant)]);
        }
        return $this->initDefined(strval($variant));
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return Interfaces\IContentDecoder
     */
    protected function checkObject(object $variant): Interfaces\IContentDecoder
    {
        if ($variant instanceof Interfaces\IContentDecoder) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppDecoderVariantIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return Interfaces\IContentDecoder
     */
    protected function initDefined(string $variant): Interfaces\IContentDecoder
    {
        try {
            /** @var class-string<Interfaces\IContentDecoder> $variant */
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance($this->getUppLang()));
            }
            throw new UploadException($this->getUppLang()->uppDecoderVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
