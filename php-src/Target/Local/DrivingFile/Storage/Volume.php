<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Volume
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage
 * Processing info file on disk volume
 */
class Volume implements Interfaces\IDrivingFile
{
    use TLang;

    protected string $keyPrefix;

    public function __construct(string $keyPrefix = '', ?Interfaces\IUppTranslations $lang = null)
    {
        $this->keyPrefix = $keyPrefix;
        $this->setUppLang($lang);
    }

    public function exists(string $key): bool
    {
        return is_file($this->fullPath($key));
    }

    public function get(string $key): string
    {
        $content = @file_get_contents($this->fullPath($key));
        if (false === $content) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRead($key));
        }
        return $content;
    }

    public function store(string $key, string $data): string
    {
        if (false === @file_put_contents($this->fullPath($key), $data)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotWrite($key));
        }
        return $key;
    }

    public function remove(string $key): bool
    {
        return @unlink($this->fullPath($key));
    }

    /**
     * @param string $key
     * @return string
     */
    protected function fullPath(string $key): string
    {
        return $this->keyPrefix . $key;
    }

    public function checkKeyEncoder(DrivingFile\KeyEncoders\AEncoder $encoder): bool
    {
        if (!$encoder instanceof Interfaces\Storages\ForVolume) {
            throw new UploadException($this->getUppLang()->uppKeyEncoderVariantIsWrong(get_class($encoder)));
        }
        return true;
    }
}
