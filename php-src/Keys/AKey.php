<?php

namespace kalanis\UploadPerPartes\Keys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use kalanis\UploadPerPartes\Uploader\Translations;


/**
 * Class AKey
 * @package kalanis\UploadPerPartes\Keys
 * Connect shared key and local details
 */
abstract class AKey
{
    /** @var Translations */
    protected $lang = null;
    /** @var TargetSearch */
    protected $target = null;
    /** @var string */
    protected $sharedKey = '';

    public function __construct(Translations $lang, TargetSearch $target)
    {
        $this->lang = $lang;
        $this->target = $target;
    }

    /**
     * @param string $key
     * @return string
     * @throws UploadException
     */
    abstract public function fromSharedKey(string $key): string;

    /**
     * @return $this
     * @throws UploadException
     */
    abstract public function generateKeys(): self;

    /**
     * @return string
     * @throws UploadException
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
            throw new UploadException($this->lang->sharedKeyIsEmpty());
        }
    }
}
