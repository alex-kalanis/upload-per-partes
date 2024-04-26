<?php

namespace kalanis\UploadPerPartes\Traits;


use kalanis\UploadPerPartes\Interfaces;


/**
 * Trait TLangInit
 * @package kalanis\UploadPerPartes\Traits
 * Initialize only lang
 */
trait TLangInit
{
    use TLang;

    public function __construct(?Interfaces\IUppTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }
}
