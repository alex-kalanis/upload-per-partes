<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\UploadPerPartes\Interfaces\IInfoStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class AStorage
 * @package kalanis\UploadPerPartes\InfoStorage
 * Target storage for data stream
 */
abstract class AStorage implements IInfoStorage
{
    use TLang;

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }
}
