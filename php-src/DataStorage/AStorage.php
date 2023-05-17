<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Interfaces\IDataStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class AStorage
 * @package kalanis\UploadPerPartes\DataStorage
 * Target storage for data stream
 */
abstract class AStorage implements IDataStorage
{
    use TLang;

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }
}
