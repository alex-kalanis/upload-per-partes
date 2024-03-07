<?php

namespace kalanis\UploadPerPartes\ServerData;


use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;

/**
 * Class AModifiers
 * @package kalanis\UploadPerPartes\ServerData
 * Init modifiers used in managing keys and data
 */
abstract class AModifiers
{
    use TLang;

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }
}
