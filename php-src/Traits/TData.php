<?php

namespace kalanis\UploadPerPartes\Traits;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoFormat;


/**
 * Trait TData
 * @package kalanis\UploadPerPartes\Traits
 * Translations
 */
trait TData
{
    use TLang;

    /** @var null|InfoFormat\Data */
    protected $data = null;

    public function setInfoData(?InfoFormat\Data $data = null): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @throws UploadException
     * @return InfoFormat\Data
     */
    public function getInfoData(): InfoFormat\Data
    {
        if (empty($this->data)) {
            throw new UploadException($this->getUppLang()->uppDriveDataNotSet());
        }
        return $this->data;
    }
}
