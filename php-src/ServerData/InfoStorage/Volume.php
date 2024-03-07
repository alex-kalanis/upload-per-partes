<?php

namespace kalanis\UploadPerPartes\ServerData\InfoStorage;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;


/**
 * Class Volume
 * @package kalanis\UploadPerPartes\ServerData\InfoStorage
 * Processing info file on disk volume
 */
class Volume extends AStorage
{
    public function exists(string $key): bool
    {
        return is_file($key);
    }

    public function load(string $key): string
    {
        $content = @file_get_contents($key);
        if (false === $content) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRead($key));
        }
        return $content;
    }

    public function save(string $key, string $data): bool
    {
        if (false === @file_put_contents($key, $data)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
        }
        return true;
    }

    public function remove(string $key): bool
    {
        if (!@unlink($key)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($key));
        }
        return true;
    }

    public function checkKeyClasses(object $limitData, object $storageKeys, object $infoFormat): bool
    {
        if (!$limitData instanceof Interfaces\InfoStorage\ForVolume) {
            throw new UploadException($this->getUppLang()->uppKeyModifierIsWrong(get_class($limitData)));
        }
        if (!$storageKeys instanceof Interfaces\InfoStorage\ForVolume) {
            throw new UploadException($this->getUppLang()->uppKeyVariantIsWrong(get_class($storageKeys)));
        }
        if (!$infoFormat instanceof Interfaces\InfoStorage\ForVolume) {
            throw new UploadException($this->getUppLang()->uppDriveFileVariantIsWrong(get_class($infoFormat)));
        }
        return true;
    }
}
