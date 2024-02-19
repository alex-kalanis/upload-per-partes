<?php

namespace kalanis\UploadPerPartes\GenerateKeys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IDriverLocation;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class AKey
 * @package kalanis\UploadPerPartes\GenerateKeys
 * Generate key which will represent upload for both client and server
 */
abstract class AKey
{
    use TLang;

    public function __construct(IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    /**
     * @param IDriverLocation $data
     * @throws UploadException
     * @return string
     */
    abstract public function generateKey(IDriverLocation $data): string;
}
