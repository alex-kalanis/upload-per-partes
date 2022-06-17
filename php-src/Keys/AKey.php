<?php

namespace kalanis\UploadPerPartes\Keys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Uploader\TargetSearch;


/**
 * Class AKey
 * @package kalanis\UploadPerPartes\Keys
 * Connect shared key and local details
 */
abstract class AKey
{
    /** @var IUPPTranslations */
    protected $lang = null;
    /** @var TargetSearch */
    protected $target = null;
    /** @var string */
    protected $sharedKey = '';

    public function __construct(IUPPTranslations $lang, TargetSearch $target)
    {
        $this->lang = $lang;
        $this->target = $target;
    }

    /**
     * @param string $key
     * @throws UploadException
     * @return string
     */
    abstract public function fromSharedKey(string $key): string;

    /**
     * @throws UploadException
     * @return $this
     */
    abstract public function generateKeys(): self;

    /**
     * @throws UploadException
     * @return string
     */
    public function getSharedKey(): string
    {
        $this->checkSharedKey();
        return $this->sharedKey;
    }

    /**
     * @throws UploadException
     */
    protected function checkSharedKey(): void
    {
        if (empty($this->sharedKey)) {
            throw new UploadException($this->lang->uppSharedKeyIsEmpty());
        }
    }
}
