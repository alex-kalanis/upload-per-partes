<?php

namespace UploadPerPartes;

use UploadPerPartes\Exceptions;

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
    /** @var Translations */
    protected $lang = null;

    public function __construct(Translations $lang, Storage\AStorage $storage, DataFormat\AFormat $format)
    {
        $this->storage = $storage;
        $this->format = $format;
        $this->lang = $lang;
    }

    /**
     * Create new drive file
     * @param string $key
     * @param DataFormat\Data $data
     * @param bool $isNew
     * @return bool
     * @throws Exceptions\UploadException
     * @throws Exceptions\ContinuityUploadException
     */
    public function write(string $key, DataFormat\Data $data, bool $isNew = false)
    {
        if ($isNew && $this->storage->exists($key)) {
            throw new Exceptions\ContinuityUploadException($this->lang->driveFileAlreadyExists());
        }
        $this->storage->save($key, $this->format->toFormat($data));
        return true;
    }

    /**
     * Read drive file
     * @param string $key
     * @return DataFormat\Data
     * @throws Exceptions\UploadException
     */
    public function read(string $key): DataFormat\Data
    {
        return $this->format->fromFormat($this->storage->load($key));
    }

    /**
     * Update upload info
     * @param string $key
     * @param DataFormat\Data $data
     * @param int $last
     * @param bool $checkContinuous
     * @return bool
     * @throws Exceptions\UploadException
     */
    public function updateLastPart(string $key, DataFormat\Data $data, int $last, bool $checkContinuous = true): bool
    {
        if ($checkContinuous) {
            if (($data->lastKnownPart + 1) != $last) {
                throw new Exceptions\UploadException($this->lang->driveFileNotContinuous());
            }
        }
        $data->lastKnownPart = $last;
        $this->storage->save($key, $this->format->toFormat($data));
        return true;
    }

    /**
     * Delete drive file - usually on finish or discard
     * @param string $key
     * @return bool
     * @throws Exceptions\UploadException
     */
    public function remove(string $key): bool
    {
        $this->storage->remove($key);
        return true;
    }
}