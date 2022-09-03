<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Keys;


/**
 * Class DriveFile
 * @package kalanis\UploadPerPartes
 * Processing drive file
 */
class DriveFile
{
    /** @var Interfaces\IInfoStorage */
    protected $storage = null;
    /** @var Interfaces\IInfoFormatting */
    protected $format = null;
    /** @var Keys\AKey */
    protected $key = null;
    /** @var Interfaces\IUPPTranslations */
    protected $lang = null;

    public function __construct(Interfaces\IUPPTranslations $lang, Interfaces\IInfoStorage $storage, Interfaces\IInfoFormatting $format, Keys\AKey $key)
    {
        $this->storage = $storage;
        $this->format = $format;
        $this->lang = $lang;
        $this->key = $key;
    }

    /**
     * Create new drive file
     * @param string $sharedKey
     * @param InfoFormat\Data $data
     * @param bool $isNew
     * @throws Exceptions\UploadException
     * @throws Exceptions\ContinuityUploadException
     * @return bool
     */
    public function write(string $sharedKey, InfoFormat\Data $data, bool $isNew = false): bool
    {
        if ($isNew && $this->exists($sharedKey)) {
            throw new Exceptions\ContinuityUploadException($this->lang->uppDriveFileAlreadyExists($sharedKey));
        }
        $this->storage->save($this->key->fromSharedKey($sharedKey), $this->format->toFormat($data));
        return true;
    }

    /**
     * Read drive file
     * @param string $sharedKey
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function read(string $sharedKey): InfoFormat\Data
    {
        return $this->format->fromFormat($this->storage->load($this->key->fromSharedKey($sharedKey)));
    }

    /**
     * Update upload info
     * @param string $sharedKey
     * @param InfoFormat\Data $data
     * @param int<0, max> $last
     * @param bool $checkContinuous
     * @throws Exceptions\UploadException
     * @return bool
     */
    public function updateLastPart(string $sharedKey, InfoFormat\Data $data, int $last, bool $checkContinuous = true): bool
    {
        if ($checkContinuous) {
            if (($data->lastKnownPart + 1) != $last) {
                throw new Exceptions\UploadException($this->lang->uppDriveFileNotContinuous($sharedKey));
            }
        }
        $data->lastKnownPart = $last;
        $this->storage->save($this->key->fromSharedKey($sharedKey), $this->format->toFormat($data));
        return true;
    }

    /**
     * Delete drive file - usually on finish or discard
     * @param string $sharedKey
     * @throws Exceptions\UploadException
     * @return bool
     */
    public function remove(string $sharedKey): bool
    {
        $this->storage->remove($this->key->fromSharedKey($sharedKey));
        return true;
    }

    /**
     * Has driver data? Mainly for testing
     * @param string $sharedKey
     * @throws Exceptions\UploadException
     * @return bool
     */
    public function exists(string $sharedKey): bool
    {
        return $this->storage->exists($this->key->fromSharedKey($sharedKey));
    }
}
