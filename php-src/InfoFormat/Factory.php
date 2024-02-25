<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IInfoFormatting;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\InfoFormat
 * Drive file format - Factory to get formats
 */
class Factory
{
    use TLang;

    const FORMAT_TEXT = 1;
    const FORMAT_JSON = 2;
    const FORMAT_LINE = 3;

    /** @var array<int, class-string<IInfoFormatting>> */
    protected $map = [
        self::FORMAT_TEXT => Text::class,
        self::FORMAT_JSON => Json::class,
        self::FORMAT_LINE => Line::class,
    ];

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param int|string|object $variant
     * @throws UploadException
     * @return IInfoFormatting
     */
    public function getFormat($variant): IInfoFormatting
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
     * @return IInfoFormatting
     */
    protected function checkObject(object $variant): IInfoFormatting
    {
        if ($variant instanceof IInfoFormatting) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return IInfoFormatting
     */
    protected function initDefined(string $variant): IInfoFormatting
    {
        try {
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance());
            }
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
