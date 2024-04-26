<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile
 * Which driving file configuration will be used
 */
class Factory
{
    use TLangInit;

    /**
     * @param Config $config
     * @throws UploadException
     * @return DrivingFile
     */
    public function getDrivingFile(Config $config): DrivingFile
    {
        $storage = (new Storage\Factory($this->getUppLang()))->getStorage($config);
        $keyEncoder = (new KeyEncoders\Factory($this->getUppLang()))->getKeyEncoder($config);
        $storage->checkKeyEncoder($keyEncoder);
        return new DrivingFile(
            $storage,
            $keyEncoder,
            (new KeyModifiers\Factory($this->getUppLang()))->getKeyModifier($config->keyModifier),
            (new DataEncoders\Factory($this->getUppLang()))->getDataEncoder($config),
            (new DataModifiers\Factory($this->getUppLang()))->getDataModifier($config->dataModifier)
        );
    }
}
