<?php

namespace kalanis\UploadPerPartes\ServerData\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IInfoStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\ServerData\InfoStorage
 * Key data storage - Factory to get where to store info data
 */
class Factory
{
    use TLang;

    const FORMAT_VOLUME = 1;
    // adapters - need to pass them
//    const FORMAT_STORAGE = 2;
//    const FORMAT_FILES = 3;
//    const FORMAT_REDIS = 4;
//    const FORMAT_PREDIS = 5;

    /** @var array<int, class-string<IInfoStorage>> */
    protected $map = [
        self::FORMAT_VOLUME => Volume::class,
//        self::FORMAT_STORAGE => Storage::class,
//        self::FORMAT_FILES => Files::class,
//        self::FORMAT_REDIS => Redis::class,
//        self::FORMAT_PREDIS => Predis::class,
    ];

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param int|class-string<IInfoStorage>|object|string $variant
     * @throws UploadException
     * @return IInfoStorage
     */
    public function getFormat($variant): IInfoStorage
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
        throw new UploadException($this->getUppLang()->uppDriveFileStorageNotSet());
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return IInfoStorage
     */
    protected function checkObject(object $variant): IInfoStorage
    {
        if ($variant instanceof IInfoStorage) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppDriveFileStorageIsWrong(get_class($variant)));
    }

    /**
     * @param class-string<IInfoStorage>|string $variant
     * @throws UploadException
     * @return IInfoStorage
     */
    protected function initDefined($variant): IInfoStorage
    {
        try {
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance($this->uppLang));
            }
            throw new UploadException($this->getUppLang()->uppDriveFileStorageIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
