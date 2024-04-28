<?php

namespace kalanis\UploadPerPartes\Target\Local\ContentDecoders;


use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\ContentDecoders
 * Which decoder will be used for reconstruction of data passed from client
 */
class Factory
{
    use TLang;

    public const FORMAT_BASE64 = '1';
    public const FORMAT_HEX = '2';
    public const FORMAT_RAW = '3';

    /** @var array<string|int, class-string<Interfaces\IContentDecoder>> */
    protected array $map = [
        self::FORMAT_BASE64 => Base64::class,
        self::FORMAT_HEX => Hex::class,
        self::FORMAT_RAW => Raw::class,
    ];

    public function __construct(?Interfaces\IUppTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param Config $config
     * @throws UploadException
     * @return Interfaces\IContentDecoder
     */
    public function getDecoder(Config $config): Interfaces\IContentDecoder
    {
        $variant = $config->decoder ?? self::FORMAT_BASE64;
        if (is_object($variant)) {
            return $this->checkObject($variant);
        }
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
