<?php

namespace kalanis\UploadPerPartes\InfoStorage;


use kalanis\UploadPerPartes\Interfaces\IInfoStorage;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class AStorage
 * @package kalanis\UploadPerPartes\InfoStorage
 * Target storage for data stream
 */
abstract class AStorage implements IInfoStorage
{
    /** @var IUPPTranslations */
    protected $lang = null;

    public function __construct(IUPPTranslations $lang)
    {
        $this->lang = $lang;
    }
}
