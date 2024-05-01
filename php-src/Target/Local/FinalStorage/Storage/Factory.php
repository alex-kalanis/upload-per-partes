<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\Storage;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\UploadPerPartes\Interfaces\IFinalStorage;
use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\Storage
 * Content data storage - Factory to get where to store main data
 */
class Factory
{
    use TLangInit;

    /**
     * @param Config $config
     * @throws UploadException
     * @return IFinalStorage
     */
    public function getStorage(Config $config): IFinalStorage
    {
        if ($config->finalStorage instanceof IFinalStorage) {
            return $config->finalStorage;
        }
        if ($config->finalStorage instanceof IStorage) {
            return new Storage($config->finalStorage, $config->targetDir, $this->getUppLang());
        }
        if ($config->finalStorage instanceof CompositeAdapter) {
            try {
                $ap = new ArrayPath();
                return new Files($config->finalStorage, $ap->setString($config->targetDir)->getArray(), $ap, $this->getUppLang());
                // @codeCoverageIgnoreStart
            } catch (PathsException $ex) {
                throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
            }
            // @codeCoverageIgnoreEnd
        }

        switch ($config->finalStorage) {
            case 'volume':
                return new Volume($config->targetDir, $this->getUppLang());
            default:
                if (is_string($config->finalStorage)) {
                    return new Volume($config->finalStorage, $this->getUppLang());
                }

                throw new UploadException($this->getUppLang()->uppFinalStorageNotSet());
        }
    }
}
