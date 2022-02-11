<?php

namespace kalanis\UploadPerPartes\InfoFormat;


/**
 * Class Data
 * @package kalanis\UploadPerPartes\DriveFile
 * Driver metadata about processed file
 */
class Data
{
    public $fileName = '';
    public $tempLocation = '';
    public $fileSize = 0;
    public $partsCount = 0;
    public $bytesPerPart = 0;
    public $lastKnownPart = 0;

    public static function init()
    {
        return new static();
    }

    public function setData(string $fileName, string $tempLocation, int $fileSize, int $partsCount = 0, int $bytesPerPart = 0, int $lastKnownPart = 0)
    {
        $this->fileName = $fileName; // final file path
        $this->tempLocation = $tempLocation; // path to temp file
        $this->fileSize = $fileSize; // final size
        $this->partsCount = $partsCount; // is on parts...
        $this->bytesPerPart = $bytesPerPart; // how long is single part
        $this->lastKnownPart = $lastKnownPart; // how many parts has been obtained
        return $this;
    }

    public function sanitizeData()
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
