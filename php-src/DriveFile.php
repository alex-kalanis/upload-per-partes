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
    /** @var DriveFile\ADriveFile */
    protected $libDriver = null;
    /** @var Translations */
    protected $lang = null;

    public function __construct(Translations $lang, DriveFile\ADriveFile $libDriver)
    {
        $this->libDriver = $libDriver;
        $this->lang = $lang;
    }

    /**
     * Create new drive file
     * @param DriveFile\Data $data
     * @return bool
     * @throws Exceptions\UploadException
     * @throws Exceptions\ContinuityUploadException
     */
    public function create(DriveFile\Data $data)
    {
        if ($this->libDriver->exists()) {
            throw new Exceptions\ContinuityUploadException($this->lang->driveFileAlreadyExists());
        }
        $this->libDriver->save($data);
        return true;
    }

    /**
     * Read drive file
     * @return DriveFile\Data
     * @throws Exceptions\UploadException
     */
    public function read(): DriveFile\Data
    {
        return $this->libDriver->load();
    }

    /**
     * Update upload info
     * @param int $last
     * @return bool
     * @throws Exceptions\UploadException
     */
    public function updateLastPart(int $last): bool
    {
        $data = $this->libDriver->load();
        if (($data->lastKnownPart + 1) != $last) {
            throw new Exceptions\UploadException($this->lang->driveFileNotContinuous());
        }
        $data->lastKnownPart = $last;
        $this->libDriver->save($data);
        return true;
    }

    /**
     * Delete drive file - usually on finish or discard
     * @return bool
     * @throws Exceptions\UploadException
     */
    public function remove(): bool
    {
        $this->libDriver->remove();
        return true;
    }
}