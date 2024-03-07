<?php

namespace kalanis\UploadPerPartes\Traits;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData;


/**
 * Trait TData
 * @package kalanis\UploadPerPartes\Traits
 * Translations
 */
trait TData
{
    use TLang;

    /** @var null|ServerData\Data */
    protected $data = null;

    public function setInfoData(?ServerData\Data $data = null): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @throws UploadException
     * @return ServerData\Data
     */
    public function getInfoData(): ServerData\Data
    {
        if (empty($this->data)) {
            throw new UploadException($this->getUppLang()->uppDriveDataNotSet());
        }
        return $this->data;
    }
}
