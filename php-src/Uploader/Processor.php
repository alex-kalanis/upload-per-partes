<?php

namespace UploadPerPartes\Uploader;

use UploadPerPartes\DataFormat;
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
    /** @var Translations */
    protected $lang = null;

    /**
     * @param Translations $lang
     * @param DriveFile $driver
     */
    public function __construct(Translations $lang, DriveFile $driver)
    {
        $this->lang = $lang;
        $this->driver = $driver;
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
        if (! @unlink($data->tempLocation)) {
            throw new Exceptions\UploadException($this->lang->cannotRemoveData());
        }
        $this->driver->remove($sharedKey);
    }

    /**
     * Upload file by parts, final status
     * @param string $sharedKey
     * @return DataFormat\Data
     * @throws Exceptions\UploadException
     */
    public function done(string $sharedKey): DataFormat\Data
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
     * @return DataFormat\Data
     * @throws Exceptions\UploadException
     */
    public function upload(string $sharedKey, string $content, ?int $segment = null): DataFormat\Data
    {
        $data = $this->driver->read($sharedKey);

        if (!is_numeric($segment)) {
            $segment = $data->lastKnownPart + 1;
            $this->saveFilePart($data, $content);
            $this->driver->updateLastPart($sharedKey, $data, $segment);
        } else {
            if ($segment > $data->lastKnownPart + 1) {
                throw new Exceptions\UploadException($this->lang->readTooEarly());
            }
            $this->saveFilePart($data, $content, $segment * $data->bytesPerPart);
        }

        return $data;
    }

    /**
     * Delete problematic segments
     * @param string $sharedKey
     * @param int $segment
     * @return DataFormat\Data
     * @throws Exceptions\UploadException
     */
    public function truncateFrom(string $sharedKey, int $segment): DataFormat\Data
    {
        $data = $this->driver->read($sharedKey);
        $this->checkSegment($data, $segment);

        $handle = fopen($data->tempLocation, 'r+');
        if (!ftruncate($handle, $data->bytesPerPart * $segment)) {
            fclose($handle);
            throw new Exceptions\UploadException($this->lang->cannotTruncateFile());
        }
        rewind($handle);
        fclose($handle);
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

        return md5(file_get_contents(
            $data->tempLocation,
            false,
            null,
            $data->bytesPerPart * $segment,
            $data->bytesPerPart
        ));
    }

    /**
     * Upload file by parts, create driving file, returns correct one (because it can exist)
     * @param DataFormat\Data $dataPack
     * @param string $sharedKey
     * @return DataFormat\Data
     * @throws Exceptions\UploadException
     */
    public function init(DataFormat\Data $dataPack, string $sharedKey): DataFormat\Data
    {
        try {
            $this->driver->write($sharedKey, $dataPack, true);
        } catch (Exceptions\ContinuityUploadException $e) { // navazani na predchozi - datapack uz mame, tak ho nacpeme na front
            $dataPack = $this->driver->read($sharedKey);
        }
        return $dataPack;
    }

    /**
     * @param DataFormat\Data $data
     * @param string $content binary content
     * @param int|null $seek where it save
     * @return bool
     * @throws Exceptions\UploadException
     */
    protected function saveFilePart(DataFormat\Data $data, string $content, ?int $seek = null)
    {
        if (is_numeric($seek)) {
            $pointer = fopen($data->tempLocation, 'wb');
            if (false === $pointer) {
                throw new Exceptions\UploadException($this->lang->cannotOpenFile());
            }
            $position = fseek($pointer, $seek);
            if ($position == -1) {
                throw new Exceptions\UploadException($this->lang->cannotSeekFile());
            }
            if (false === fwrite($pointer, $content, strlen($content))) {
                throw new Exceptions\UploadException($this->lang->cannotWriteFile());
            }
            fclose($pointer);
        } else {
            $pointer = fopen($data->tempLocation, 'ab');
            if (false == $pointer) {
                throw new Exceptions\UploadException($this->lang->cannotOpenFile());
            }
            if (false === fwrite($pointer, $content, strlen($content))) {
                throw new Exceptions\UploadException($this->lang->cannotWriteFile());
            }
            fclose($pointer);
        }
        return true;
    }

    /**
     * @param DataFormat\Data $data
     * @param int $segment
     * @throws Exceptions\UploadException
     */
    protected function checkSegment(DataFormat\Data $data, int $segment): void
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