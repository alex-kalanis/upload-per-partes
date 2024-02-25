<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IDataStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\DataStorage
 * Data data storage - Factory to get where to temporary store posted data
 */
class Factory
{
    use TLang;

    const FORMAT_VOLUME = 1;
    // adapters - need to pass them
//    const FORMAT_FILES = 2;

    /** @var array<int, class-string<IInfoFormatting>> */
    protected $map = [
        self::FORMAT_VOLUME => VolumeBasic::class,
//        self::FORMAT_FILES => Files::class,
    ];

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param int|string|object $variant
     * @throws UploadException
     * @return IDataStorage
     */
    public function getFormat($variant): IDataStorage
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
        throw new UploadException($this->getUppLang()->uppTemporaryStorageNotSet());
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return IDataStorage
     */
    protected function checkObject(object $variant): IDataStorage
    {
        if ($variant instanceof IDataStorage) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppTemporaryStorageIsWrong(get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return IDataStorage
     */
    protected function initDefined(string $variant): IDataStorage
    {
        try {
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance($this->uppLang));
            }
            throw new UploadException($this->getUppLang()->uppTemporaryStorageIsWrong($variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
