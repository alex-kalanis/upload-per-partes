<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage
 * Which final storage configuration will be used
 */
class Factory
{
    use TLangInit;

    /**
     * @param Config $config
     * @throws UploadException
     * @return FinalStorage
     */
    public function getFinalStorage(Config $config): FinalStorage
    {
        return new FinalStorage(
            (new Storage\Factory($this->getUppLang()))->getStorage($config),
            (new KeyEncoders\Factory($this->getUppLang()))->getKeyEncoder($config)
        );
    }
}
