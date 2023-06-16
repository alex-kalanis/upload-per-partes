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
 * @package kalanis\UploadPerPartes\DriveFile
 * Drive file format - Factory to get formats
 */
class Factory
{
    use TLang;

    const FORMAT_TEXT = 1;
    const FORMAT_JSON = 2;

    /** @var array<int, class-string<IInfoFormatting>> */
    protected static $map = [
        self::FORMAT_TEXT => Text::class,
        self::FORMAT_JSON => Json::class,
    ];

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param int $variant
     * @throws UploadException
     * @return IInfoFormatting
     */
    public function getFormat(int $variant): IInfoFormatting
    {
        if (!isset(static::$map[$variant])) {
            throw new UploadException($this->getUppLang()->uppDriveFileVariantNotSet());
        }
        $class = static::$map[$variant];
        try {
            $ref = new ReflectionClass($class);
            if ($ref->isInstantiable()) {
                $lib = $ref->newInstance();
                if ($lib instanceof IInfoFormatting) {
                    return $lib;
                }
            }
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong($class));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
