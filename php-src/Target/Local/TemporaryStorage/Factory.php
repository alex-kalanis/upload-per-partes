<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage
 * Which temporary storage configuration will be used
 */
class Factory
{
    use TLangInit;

    /**
     * @param Config $config
     * @throws UploadException
     * @return TemporaryStorage
     */
    public function getTemporaryStorage(Config $config): TemporaryStorage
    {
        return new TemporaryStorage(
            (new Storage\Factory($this->getUppLang()))->getStorage($config),
            (new KeyEncoders\Factory($this->getUppLang()))->getKeyEncoder($config),
            $this->getUppLang()
        );
    }
}
