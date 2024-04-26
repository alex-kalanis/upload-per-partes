<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\UploadPerPartes\Interfaces\IDrivingFile;
use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage
 * Key data storage - Factory to get where to store info data
 */
class Factory
{
    use TLangInit;

    public const FORMAT_VOLUME = 1;
    public const FORMAT_STORAGE = 2;
    public const FORMAT_FILES = 3;
    public const FORMAT_REDIS = 4;
    public const FORMAT_PREDIS = 5;
    public const FORMAT_CLIENT = 6;

    /**
     * @param Config $config
     * @throws UploadException
     * @return IDrivingFile
     */
    public function getStorage(Config $config): IDrivingFile
    {
        if ($config->drivingFileStorage instanceof IDrivingFile) {
            return $config->drivingFileStorage;
        }
        if ($config->drivingFileStorage instanceof IStorage) {
            return new Storage($config->drivingFileStorage, $config->tempDir, $this->getUppLang());
        }
        if ($config->drivingFileStorage instanceof CompositeAdapter) {
            try {
                $ap = new ArrayPath();
                return new Files($config->drivingFileStorage, $ap->setString($config->tempDir)->getArray(), $ap, $this->getUppLang());
                // @codeCoverageIgnoreStart
            } catch (PathsException $ex) {
                throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
            }
            // @codeCoverageIgnoreEnd
        }

        switch ($config->drivingFileStorage) {
            case 'volume':
            case self::FORMAT_VOLUME:
                return new Volume($config->tempDir, $this->getUppLang());
            case 'client':
            case self::FORMAT_CLIENT:
                return new Client($this->getUppLang());
            default:
                if (is_string($config->drivingFileStorage)) {
                    return new Volume($config->drivingFileStorage, $this->getUppLang());
                }

                throw new UploadException($this->getUppLang()->uppDriveFileStorageNotSet());
        }
    }
}
