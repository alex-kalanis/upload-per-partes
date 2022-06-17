<?php

namespace kalanis\UploadPerPartes\InfoFormat;


/**
 * Class Data
 * @package kalanis\UploadPerPartes\DriveFile
 * Driver metadata about processed file
 */
final class Data
{
    /** @var string */
    public $fileName = '';
    /** @var string */
    public $tempLocation = '';
    /** @var int */
    public $fileSize = 0;
    /** @var int */
    public $partsCount = 0;
    /** @var int */
    public $bytesPerPart = 0;
    /** @var int */
    public $lastKnownPart = 0;

    public static function init(): self
    {
        return new static();
    }

    /**
     * @param string $fileName
     * @param string $tempLocation
     * @param int $fileSize
     * @param int $partsCount
     * @param int $bytesPerPart
     * @param int $lastKnownPart
     * @return $this
     */
    public function setData(string $fileName, string $tempLocation, int $fileSize, int $partsCount = 0, int $bytesPerPart = 0, int $lastKnownPart = 0): self
    {
        $this->fileName = $fileName; // final file path
        $this->tempLocation = $tempLocation; // path to temp file
        $this->fileSize = $fileSize; // final size
        $this->partsCount = $partsCount; // is on parts...
        $this->bytesPerPart = $bytesPerPart; // how long is single part
        $this->lastKnownPart = $lastKnownPart; // how many parts has been obtained
        return $this;
    }

    public function sanitizeData(): self
    {
        $this->fileName = (string)$this->fileName;
        $this->tempLocation = (string)$this->tempLocation;
        $this->fileSize = (int)$this->fileSize;
        $this->partsCount = (int)$this->partsCount;
        $this->bytesPerPart = (int)$this->bytesPerPart;
        $this->lastKnownPart = (int)$this->lastKnownPart;
        return $this;
    }
}
