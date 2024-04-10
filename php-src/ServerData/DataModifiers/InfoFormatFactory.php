<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class InfoFormatFactory
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Drive file format - Factory to get formats
 *
 * How to pack and unpack data in info file on server
 */
class InfoFormatFactory
{
    use TLang;

    public const FORMAT_TEXT = 1;
    public const FORMAT_JSON = 2;
    public const FORMAT_LINE = 3;
    public const FORMAT_SERIAL = 4;

    /** @var array<int, class-string<Interfaces\IInfoFormatting>> */
    protected array $map = [
        self::FORMAT_TEXT => Text::class,
        self::FORMAT_JSON => Json::class,
        self::FORMAT_LINE => Line::class,
        self::FORMAT_SERIAL => Serialize::class,
    ];

    public function __construct(?Interfaces\IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param int|class-string<Interfaces\IInfoFormatting>|object|string $variant
     * @throws UploadException
     * @return Interfaces\IInfoFormatting
     */
    public function getFormat($variant): Interfaces\IInfoFormatting
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
        throw new UploadException($this->getUppLang()->uppDriveFileVariantNotSet());
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return Interfaces\IInfoFormatting
     */
    protected function checkObject(object $variant): Interfaces\IInfoFormatting
    {
        if ($variant instanceof Interfaces\IInfoFormatting) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return Interfaces\IInfoFormatting
     */
    protected function initDefined(string $variant): Interfaces\IInfoFormatting
    {
        try {
            /** @var class-string<Interfaces\IInfoFormatting> $variant */
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance($this->getUppLang()));
            }
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
