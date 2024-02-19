<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerKeys;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class DriveFile
 * @package kalanis\UploadPerPartes
 * Processing drive file
 */
class DriveFile
{
    use TLang;

    /** @var Interfaces\IInfoStorage */
    protected $storage = null;
    /** @var Interfaces\IInfoFormatting */
    protected $format = null;
    /** @var ServerKeys\AKey */
    protected $key = null;

    public function __construct(
        Interfaces\IInfoStorage $storage,
        Interfaces\IInfoFormatting $format,
        ServerKeys\AKey $key,
        ?Interfaces\IUPPTranslations $lang = null
    )
    {
        $this->setUppLang($lang);
        $this->storage = $storage;
        $this->format = $format;
        $this->key = $key;
    }

    /**
     * Create new drive file
     * @param InfoFormat\Data $data
     * @param bool $isNew
     * @throws Exceptions\UploadException
     * @throws Exceptions\ContinuityUploadException
     * @return bool
     */
    public function write(InfoFormat\Data $data, bool $isNew = false): bool
    {
        if ($isNew && $this->exists($data)) {
            throw new Exceptions\ContinuityUploadException($this->getUppLang()->uppDriveFileAlreadyExists($data->driverName));
        }
        $this->storage->save($this->key->fromData($data), $this->format->toFormat($data));
        return true;
    }

    /**
     * Read drive file
     * @param Interfaces\IDriverLocation $data
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function read(Interfaces\IDriverLocation $data): InfoFormat\Data
    {
        return $this->format->fromFormat($this->storage->load($this->key->fromData($data)));
    }

    /**
     * Update upload info
     * @param InfoFormat\Data $data
     * @param int<0, max> $last
     * @param bool $checkContinuous
     * @throws Exceptions\UploadException
     * @return bool
     */
    public function updateLastPart(InfoFormat\Data $data, int $last, bool $checkContinuous = true): bool
    {
        if ($checkContinuous) {
            if (($data->lastKnownPart + 1) != $last) {
                throw new Exceptions\UploadException($this->getUppLang()->uppDriveFileNotContinuous($data->getDriverKey()));
            }
        }
        $data->lastKnownPart = $last;
        $this->storage->save($this->key->fromData($data), $this->format->toFormat($data));
        return true;
    }

    /**
     * Delete drive file - usually on finish or discard
     * @param Interfaces\IDriverLocation $data
     * @throws Exceptions\UploadException
     * @return bool
     */
    public function remove(Interfaces\IDriverLocation $data): bool
    {
        $this->storage->remove($this->key->fromData($data));
        return true;
    }

    /**
     * Has driver data? Mainly for testing
     * @param Interfaces\IDriverLocation $data
     * @throws Exceptions\UploadException
     * @return bool
     */
    public function exists(Interfaces\IDriverLocation $data): bool
    {
        return $this->storage->exists($this->key->fromData($data));
    }
}
