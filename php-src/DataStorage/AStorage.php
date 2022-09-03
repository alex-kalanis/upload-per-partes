<?php

namespace kalanis\UploadPerPartes\DataStorage;


use kalanis\UploadPerPartes\Interfaces\IDataStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class AStorage
 * @package kalanis\UploadPerPartes\DataStorage
 * Target storage for data stream
 */
abstract class AStorage implements IDataStorage
{
    /** @var IUPPTranslations */
    protected $lang = null;

    public function __construct(IUPPTranslations $lang)
    {
        $this->lang = $lang;
    }
}
