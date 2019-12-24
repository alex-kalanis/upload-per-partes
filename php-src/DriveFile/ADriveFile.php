<?php

namespace UploadPerPartes\DriveFile;

use UploadPerPartes\Exceptions\UploadException;
use UploadPerPartes\Translations;

/**
 * Class DriveFile
 * @package UploadPerPartes\DriveFile
 * Processing drive file - for each variant
 */
abstract class ADriveFile
{
    const VARIANT_TEXT = 1;
    const VARIANT_JSON = 2;

    protected $path = '';
    protected $lang = null;

    public function __construct(Translations $lang, string $path)
    {
        $this->path = $path;
        $this->lang = $lang;
    }

    public function exists(): bool
    {
        return is_file($this->path);
    }

    /**
     * @return Data
     * @throws UploadException
     */
    abstract public function load(): Data;

    /**
     * @param Data $data
     * @throws UploadException
     */
    abstract public function save(Data $data): void;

    /**
     * @throws UploadException
     */
    public function remove()
    {
        if (!unlink($this->path)) {
            throw new UploadException($this->lang->driveFileCannotRemove());
        }
        return true;
    }

    /**
     * @param Translations $lang
     * @param int $variant
     * @param string $path
     * @return ADriveFile
     * @throws UploadException
     */
    public static function init(Translations $lang, int $variant, string $path): ADriveFile
    {
        switch ($variant) {
            case static::VARIANT_TEXT:
                return new Text($lang, $path);
            case static::VARIANT_JSON:
                return new Json($lang, $path);
            default:
                throw new UploadException($lang->driveFileVariantNotSet());
        }
    }
}