<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class Processor
 * @package kalanis\UploadPerPartes
 * Processing upload per-partes
 */
class Processor
{
    use TLang;

    /** @var DriveFile */
    protected $driver = null;
    /** @var Interfaces\IDataStorage */
    protected $storage = null;
    /** @var Hashed */
    protected $hashed = null;

    public function __construct(
        DriveFile $driver,
        Interfaces\IDataStorage $storage,
        Hashed $hashed,
        ?Interfaces\IUPPTranslations $lang = null
    )
    {
        $this->setUppLang($lang);
        $this->driver = $driver;
        $this->storage = $storage;
        $this->hashed = $hashed;
    }

    /**
     * Upload file by parts, final status - cancel that
     * @param string $sharedKey
     * @throws Exceptions\UploadException
     * @return void
     */
    public function cancel(string $sharedKey): void
    {
        $data = $this->driver->read($sharedKey);
        $this->storage->remove($data->tempLocation);
        $this->driver->remove($sharedKey);
    }

    /**
     * Upload file by parts, final status
     * @param string $sharedKey
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function done(string $sharedKey): InfoFormat\Data
    {
        $data = $this->driver->read($sharedKey);
        $this->driver->remove($sharedKey);
        return $data;
    }

    /**
     * Upload file by parts, use driving file
     * @param string $sharedKey
     * @param string $content binary content
     * @param int|null $segment where it save
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function upload(string $sharedKey, string $content, ?int $segment = null): InfoFormat\Data
    {
        $data = $this->driver->read($sharedKey);

        if (!is_numeric($segment)) {
            $segment = $data->lastKnownPart + 1;
            $this->storage->addPart($data->tempLocation, $content);
            $this->driver->updateLastPart($sharedKey, $data, $segment);
        } else {
            if ($segment > $data->lastKnownPart + 1) {
                throw new Exceptions\UploadException($this->getUppLang()->uppReadTooEarly($sharedKey));
            }
            $this->storage->addPart($data->tempLocation, $content, $segment * $data->bytesPerPart);
        }

        return $data;
    }

    /**
     * Delete problematic segments
     * @param string $sharedKey
     * @param int $segment
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function truncateFrom(string $sharedKey, int $segment): InfoFormat\Data
    {
        $data = $this->driver->read($sharedKey);
        $this->checkSegment($data, $segment);
        $this->storage->truncate($data->tempLocation, $data->bytesPerPart * $segment);
        $this->driver->updateLastPart($sharedKey, $data, $segment, false);
        return $data;
    }

    /**
     * Check already uploaded parts
     * @param string $sharedKey
     * @param int $segment
     * @throws Exceptions\UploadException
     * @return string
     */
    public function check(string $sharedKey, int $segment): string
    {
        $data = $this->driver->read($sharedKey);
        $this->checkSegment($data, $segment);
        return $this->hashed->calcHash($this->storage->getPart($data->tempLocation, $data->bytesPerPart * $segment, $data->bytesPerPart));
    }

    /**
     * Upload file by parts, create driving file, returns correct one (because it can exist)
     * @param InfoFormat\Data $dataPack
     * @param string $sharedKey
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function init(InfoFormat\Data $dataPack, string $sharedKey): InfoFormat\Data
    {
        try {
            $this->driver->write($sharedKey, $dataPack, true);
        } catch (Exceptions\ContinuityUploadException $e) { // continuity from previous try - we got datapack, so we return it
            $dataPack = $this->driver->read($sharedKey);
        }
        return $dataPack;
    }

    /**
     * @param InfoFormat\Data $data
     * @param int $segment
     * @throws Exceptions\UploadException
     */
    protected function checkSegment(InfoFormat\Data $data, int $segment): void
    {
        if (0 > $segment) {
            throw new Exceptions\UploadException($this->getUppLang()->uppSegmentOutOfBounds($segment));
        }
        if ($segment > $data->partsCount) {
            throw new Exceptions\UploadException($this->getUppLang()->uppSegmentOutOfBounds($segment));
        }
        if ($segment > $data->lastKnownPart) {
            throw new Exceptions\UploadException($this->getUppLang()->uppSegmentNotUploadedYet($segment));
        }
    }
}
