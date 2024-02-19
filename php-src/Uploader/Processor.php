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
     * @param Interfaces\IDriverLocation $dataPack
     * @throws Exceptions\UploadException
     * @return void
     */
    public function cancel(Interfaces\IDriverLocation $dataPack): void
    {
        $data = $this->driver->read($dataPack);
        $this->storage->remove($data->tempLocation);
        $this->driver->remove($dataPack);
    }

    /**
     * Upload file by parts, final status
     * @param Interfaces\IDriverLocation $dataPack
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function done(Interfaces\IDriverLocation $dataPack): InfoFormat\Data
    {
        $data = $this->driver->read($dataPack);
        $this->driver->remove($dataPack);
        return $data;
    }

    /**
     * Upload file by parts, use driving file
     * @param Interfaces\IDriverLocation $dataPack
     * @param string $content binary content
     * @param int<0, max>|null $segment where it save
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function upload(Interfaces\IDriverLocation $dataPack, string $content, ?int $segment = null): InfoFormat\Data
    {
        $data = $this->driver->read($dataPack);

        if (!is_numeric($segment)) {
            $segment = $data->lastKnownPart + 1;
            $this->storage->addPart($data->tempLocation, $content);
            $this->driver->updateLastPart($data, $segment);
        } else {
            if ($segment > $data->lastKnownPart + 1) {
                throw new Exceptions\UploadException($this->getUppLang()->uppReadTooEarly($dataPack->getDriverKey()));
            }
            $this->storage->addPart($data->tempLocation, $content, $segment * $data->bytesPerPart);
        }

        return $data;
    }

    /**
     * Delete problematic segments
     * @param Interfaces\IDriverLocation $dataPack
     * @param int<0, max> $segment
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function truncateFrom(Interfaces\IDriverLocation $dataPack, int $segment): InfoFormat\Data
    {
        $data = $this->driver->read($dataPack);
        $this->checkSegment($data, $segment);
        $this->storage->truncate($data->tempLocation, $data->bytesPerPart * $segment);
        $this->driver->updateLastPart($data, $segment, false);
        return $data;
    }

    /**
     * Check already uploaded parts
     * @param Interfaces\IDriverLocation $dataPack
     * @param int<0, max> $segment
     * @throws Exceptions\UploadException
     * @return string
     */
    public function check(Interfaces\IDriverLocation $dataPack, int $segment): string
    {
        $data = $this->driver->read($dataPack);
        $this->checkSegment($data, $segment);
        return $this->hashed->calcHash($this->storage->getPart($data->tempLocation, $data->bytesPerPart * $segment, $data->bytesPerPart));
    }

    /**
     * Upload file by parts, create driving file, returns correct one (because it can exist)
     * @param InfoFormat\Data $dataPack
     * @throws Exceptions\UploadException
     * @return InfoFormat\Data
     */
    public function init(InfoFormat\Data $dataPack): InfoFormat\Data
    {
        try {
            $this->driver->write($dataPack, true);
        } catch (Exceptions\ContinuityUploadException $e) { // continuity from previous try - we got data package, so we return it
            $dataPack = $this->driver->read($dataPack);
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
