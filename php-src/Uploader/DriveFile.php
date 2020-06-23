<?php

namespace UploadPerPartes\Uploader;

use UploadPerPartes\DataFormat;
use UploadPerPartes\Exceptions;
use UploadPerPartes\Keys;
use UploadPerPartes\Storage;

/**
 * Class DriveFile
 * @package UploadPerPartes
 * Processing drive file
 */
class DriveFile
{
    /** @var Storage\AStorage */
    protected $storage = null;
    /** @var DataFormat\AFormat */
    protected $format = null;
    /** @var Keys\AKey */
    protected $key = null;
    /** @var Translations */
    protected $lang = null;

    public function __construct(Translations $lang, Storage\AStorage $storage, DataFormat\AFormat $format, Keys\AKey $key)
    {
        $this->storage = $storage;
        $this->format = $format;
        $this->lang = $lang;
        $this->key = $key;
    }

    /**
     * Create new drive file
     * @param string $sharedKey
     * @param DataFormat\Data $data
     * @param bool $isNew
     * @return bool
     * @throws Exceptions\UploadException
     * @throws Exceptions\ContinuityUploadException
     */
    public function write(string $sharedKey, DataFormat\Data $data, bool $isNew = false): bool
    {
        if ($isNew && $this->exists($sharedKey)) {
            throw new Exceptions\ContinuityUploadException($this->lang->driveFileAlreadyExists());
        }
        $this->storage->save($this->key->fromSharedKey($sharedKey), $this->format->toFormat($data));
        return true;
    }

    /**
     * Read drive file
     * @param string $sharedKey
     * @return DataFormat\Data
     * @throws Exceptions\UploadException
     */
    public function read(string $sharedKey): DataFormat\Data
    {
        return $this->format->fromFormat($this->storage->load($this->key->fromSharedKey($sharedKey)));
    }

    /**
     * Update upload info
     * @param string $sharedKey
     * @param DataFormat\Data $data
     * @param int $last
     * @param bool $checkContinuous
     * @return bool
     * @throws Exceptions\UploadException
     */
    public function updateLastPart(string $sharedKey, DataFormat\Data $data, int $last, bool $checkContinuous = true): bool
    {
        if ($checkContinuous) {
            if (($data->lastKnownPart + 1) != $last) {
                throw new Exceptions\UploadException($this->lang->driveFileNotContinuous());
            }
        }
        $data->lastKnownPart = $last;
        $this->storage->save($this->key->fromSharedKey($sharedKey), $this->format->toFormat($data));
        return true;
    }

    /**
     * Delete drive file - usually on finish or discard
     * @param string $sharedKey
     * @return bool
     * @throws Exceptions\UploadException
     */
    public function remove(string $sharedKey): bool
    {
        $this->storage->remove($this->key->fromSharedKey($sharedKey));
        return true;
    }

    /**
     * Has driver data? Mainly for testing
     * @param string $sharedKey
     * @return bool
     */
    public function exists(string $sharedKey): bool
    {
        return $this->storage->exists($this->key->fromSharedKey($sharedKey));
    }
}