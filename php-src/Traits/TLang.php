<?php

namespace kalanis\UploadPerPartes\Traits;


use kalanis\UploadPerPartes\Interfaces\IUppTranslations;
use kalanis\UploadPerPartes\Uploader\Translations;


/**
 * Trait TLang
 * @package kalanis\UploadPerPartes\Traits
 * Translations
 */
trait TLang
{
    protected ?IUppTranslations $uppLang = null;

    public function setUppLang(?IUppTranslations $lang = null): self
    {
        $this->uppLang = $lang;
        return $this;
    }

    public function getUppLang(): IUppTranslations
    {
        if (empty($this->uppLang)) {
            $this->uppLang = new Translations();
        }
        return $this->uppLang;
    }
}
