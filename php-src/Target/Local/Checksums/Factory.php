<?php

namespace kalanis\UploadPerPartes\Target\Local\Checksums;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\Checksums
 * Which checksum will be used for checking parts of content on both client and server
 */
class Factory
{
    use TLangInit;

    public const FORMAT_MD5 = '1';
    public const FORMAT_SHA1 = '2';

    /** @var array<int, class-string<Interfaces\IChecksum>> */
    protected array $map = [
        self::FORMAT_MD5 => Md5::class,
        self::FORMAT_SHA1 => Sha1::class,
    ];

    /**
     * @param Config $config
     * @throws UploadException
     * @return Interfaces\IChecksum
     */
    public function getChecksum(Config $config): Interfaces\IChecksum
    {
        $variant = $config->checksum ?? self::FORMAT_MD5;
        if (is_object($variant)) {
            return $this->checkObject($variant);
        }
        if (isset($this->map[$variant])) {
            return $this->initDefined($this->map[$variant]);
        }
        return $this->initDefined($variant);
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return Interfaces\IChecksum
     */
    protected function checkObject(object $variant): Interfaces\IChecksum
    {
        if ($variant instanceof Interfaces\IChecksum) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppChecksumVariantIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return Interfaces\IChecksum
     */
    protected function initDefined(string $variant): Interfaces\IChecksum
    {
        try {
            /** @var class-string<Interfaces\IChecksum> $variant */
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance());
            }
            throw new UploadException($this->getUppLang()->uppChecksumVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
