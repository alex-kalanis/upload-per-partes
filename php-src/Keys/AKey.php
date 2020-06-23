<?php

namespace UploadPerPartes\Keys;

use UploadPerPartes\Exceptions\UploadException;
use UploadPerPartes\Storage\TargetSearch;
use UploadPerPartes\Uploader\Translations;

/**
 * Class AKey
 * @package UploadPerPartes\Keys
 * Connect shared key and local details
 */
abstract class AKey
{
    const VARIANT_VOLUME = 1;
    const VARIANT_RANDOM = 2;
    const VARIANT_REDIS = 3;

    protected $lang = null;
    protected $target = null;
    protected $sharedKey = '';

    public function __construct(Translations $lang, TargetSearch $target)
    {
        $this->lang = $lang;
        $this->target = $target;
    }

    /**
     * @param string $key
     * @return string
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

    /**
     * @param Translations $lang
     * @param TargetSearch $target
     * @param int $variant
     * @return AKey
     * @throws UploadException
     */
    public static function getVariant(Translations $lang, TargetSearch $target, int $variant): AKey
    {
        switch ($variant) {
            case static::VARIANT_VOLUME:
                return new SimpleVolume($lang, $target);
            case static::VARIANT_RANDOM:
                return new Random($lang, $target);
            case static::VARIANT_REDIS:
                return new Redis($lang, $target);
            default:
                throw new UploadException($lang->keyVariantNotSet());
        }
    }
}