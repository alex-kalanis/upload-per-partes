<?php

namespace UploadPerPartes\Uploader;

use UploadPerPartes\InfoFormat;
use UploadPerPartes\DataStorage;
use UploadPerPartes\Exceptions;

/**
 * Class Processor
 * @package UploadPerPartes
 * Processing upload per-partes
 */
class Processor
{
    /** @var DriveFile */
    protected $driver = null;
    /** @var DataStorage\AStorage */
    protected $storage = null;
    /** @var Translations */
    protected $lang = null;

    /**
     * @param Translations $lang
     * @param DriveFile $driver
     * @param DataStorage\AStorage $storage
     */
    public function __construct(Translations $lang, DriveFile $driver, DataStorage\AStorage $storage = null)
    {
        $this->lang = $lang;
        $this->driver = $driver;
        $this->storage = $storage;
    }

    /**
     * Upload file by parts, final status
     * @param string $sharedKey
     * @return void
     * @throws Exceptions\UploadException
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
     * @return InfoFormat\Data
     * @throws Exceptions\UploadException
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
     * @return InfoFormat\Data
     * @throws Exceptions\UploadException
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
                throw new Exceptions\UploadException($this->lang->readTooEarly());
            }
            $this->storage->addPart($data->tempLocation, $content, $segment * $data->bytesPerPart);
        }

        return $data;
    }

    /**
     * Delete problematic segments
     * @param string $sharedKey
     * @param int $segment
     * @return InfoFormat\Data
     * @throws Exceptions\UploadException
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
     * @return string
     * @throws Exceptions\UploadException
     */
    public function check(string $sharedKey, int $segment): string
    {
        $data = $this->driver->read($sharedKey);
        $this->checkSegment($data, $segment);
        return md5($this->storage->getPart($data->tempLocation, $data->bytesPerPart * $segment, $data->bytesPerPart));
    }

    /**
     * Upload file by parts, create driving file, returns correct one (because it can exist)
     * @param InfoFormat\Data $dataPack
     * @param string $sharedKey
     * @return InfoFormat\Data
     * @throws Exceptions\UploadException
     */
    public function init(InfoFormat\Data $dataPack, string $sharedKey): InfoFormat\Data
    {
        try {
            $this->driver->write($sharedKey, $dataPack, true);
        } catch (Exceptions\ContinuityUploadException $e) { // navazani na predchozi - datapack uz mame, tak ho nacpeme na front
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
        if ($segment < 0) {
            throw new Exceptions\UploadException($this->lang->segmentOutOfBounds());
        }
        if ($segment > $data->partsCount) {
            throw new Exceptions\UploadException($this->lang->segmentOutOfBounds());
        }
        if ($segment > $data->lastKnownPart) {
            throw new Exceptions\UploadException($this->lang->segmentNotUploadedYet());
        }
    }
}