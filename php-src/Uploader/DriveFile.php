<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\Keys;
use kalanis\UploadPerPartes\InfoStorage;


/**
 * Class DriveFile
 * @package kalanis\UploadPerPartes
 * Processing drive file
 */
class DriveFile
{
    /** @var InfoStorage\AStorage */
    protected $storage = null;
    /** @var InfoFormat\AFormat */
    protected $format = null;
    /** @var Keys\AKey */
    protected $key = null;
    /** @var Translations */
    protected $lang = null;

    public function __construct(Translations $lang, InfoStorage\AStorage $storage, InfoFormat\AFormat $format, Keys\AKey $key)
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
     * @return bool
     * @throws Exceptions\UploadException
     * @throws Exceptions\ContinuityUploadException
     */
    public function write(string $sharedKey, InfoFormat\Data $data, bool $isNew = false): bool
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
     * @return InfoFormat\Data
     * @throws Exceptions\UploadException
     */
    public function read(string $sharedKey): InfoFormat\Data
    {
        return $this->format->fromFormat($this->storage->load($this->key->fromSharedKey($sharedKey)));
    }

    /**
     * Update upload info
     * @param string $sharedKey
     * @param InfoFormat\Data $data
     * @param int $last
     * @param bool $checkContinuous
     * @return bool
     * @throws Exceptions\UploadException
     */
    public function updateLastPart(string $sharedKey, InfoFormat\Data $data, int $last, bool $checkContinuous = true): bool
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
     * @throws Exceptions\UploadException
     */
    public function exists(string $sharedKey): bool
    {
        return $this->storage->exists($this->key->fromSharedKey($sharedKey));
    }
}
