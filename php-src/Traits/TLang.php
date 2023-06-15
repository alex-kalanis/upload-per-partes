<?php

namespace kalanis\UploadPerPartes\Traits;


use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Uploader\Translations;


/**
 * Trait TLang
 * @package kalanis\UploadPerPartes\Traits
 * Translations
 */
trait TLang
{
    /** @var IUPPTranslations|null */
    protected $uppLang = null;

    public function setUppLang(?IUPPTranslations $lang = null): self
    {
        $this->uppLang = $lang;
        return $this;
    }

    public function getUppLang(): IUPPTranslations
    {
        if (empty($this->uppLang)) {
            $this->uppLang = new Translations();
        }
        return $this->uppLang;
    }
}
