<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage\Storage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\UploadPerPartes\Interfaces\ITemporaryStorage;
use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage\Storage
 * Content data storage - Factory to get where to store main data
 */
class Factory
{
    use TLangInit;

    /**
     * @param Config $config
     * @throws UploadException
     * @return ITemporaryStorage
     */
    public function getStorage(Config $config): ITemporaryStorage
    {
        if ($config->temporaryStorage instanceof ITemporaryStorage) {
            return $config->temporaryStorage;
        }
        if ($config->temporaryStorage instanceof IStorage) {
            return new Storage($config->temporaryStorage, $config->tempDir, $this->getUppLang());
        }
        if ($config->temporaryStorage instanceof CompositeAdapter) {
            try {
                $ap = new ArrayPath();
                return new Files($config->temporaryStorage, $ap->setString($config->tempDir)->getArray(), $ap, $this->getUppLang());
                // @codeCoverageIgnoreStart
            } catch (PathsException $ex) {
                throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
            }
            // @codeCoverageIgnoreEnd
        }

        switch ($config->temporaryStorage) {
            case 'volume':
                return new Volume($config->tempDir, $this->getUppLang());
            default:
                if (is_string($config->temporaryStorage)) {
                    return new Volume($config->temporaryStorage, $this->getUppLang());
                }

                throw new UploadException($this->getUppLang()->uppTempStorageNotSet());
        }
    }
}
