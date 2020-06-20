<?php

namespace UploadPerPartes\DataFormat;

/**
 * Class Data
 * @package UploadPerPartes\DriveFile
 * Driver metadata about processed file
 */
class Data
{
    public $fileName = '';
    public $tempPath = '';
    public $fileSize = 0;
    public $partsCount = 0;
    public $bytesPerPart = 0;
    public $lastKnownPart = 0;

    public static function init()
    {
        return new static();
    }

    public function setData(string $fileName, string $tempPath, int $fileSize, int $partsCount = 0, int $bytesPerPart = 0, int $lastKnownPart = 0)
    {
        $this->fileName = $fileName;
        $this->tempPath = $tempPath;
        $this->fileSize = $fileSize;
        $this->partsCount = $partsCount;
        $this->bytesPerPart = $bytesPerPart;
        $this->lastKnownPart = $lastKnownPart;
        return $this;
    }

    public function sanitizeData()
    {
        $this->fileName = (string)$this->fileName;
        $this->tempPath = (string)$this->tempPath;
        $this->fileSize = (int)$this->fileSize;
        $this->partsCount = (int)$this->partsCount;
        $this->bytesPerPart = (int)$this->bytesPerPart;
        $this->lastKnownPart = (int)$this->lastKnownPart;
        return $this;
    }
}