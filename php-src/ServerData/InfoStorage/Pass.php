<?php

namespace kalanis\UploadPerPartes\ServerData\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;


/**
 * Class Pass
 * @package kalanis\UploadPerPartes\ServerData\InfoStorage
 * Processing info file as it came inside the passed key
 *
 * For using this option you must set both $infoFormat and $modifyData in Processor as the same class!!!
 */
class Pass extends AStorage
{
    public function exists(string $key): bool
    {
        return !empty($key);
    }

    public function load(string $key): string
    {
        return $key;
    }

    public function save(string $key, string $data): bool
    {
        return true;
    }

    public function remove(string $key): bool
    {
        return true;
    }

    public function checkKeyClasses(object $limitData, object $storageKeys, object $infoFormat): bool
    {
        if (!$limitData instanceof Interfaces\InfoStorage\ForPass) {
            throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong(get_class($limitData)));
        }
        if (!$storageKeys instanceof Interfaces\InfoStorage\ForPass) {
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($storageKeys)));
        }
        if (!$infoFormat instanceof Interfaces\InfoStorage\ForPass) {
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($infoFormat)));
        }
        if ($limitData != $infoFormat) {
            // for possibility of decoding these two variables must point to one class
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($infoFormat)));
        }
        return true;
    }
}
