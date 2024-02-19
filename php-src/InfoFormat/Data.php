<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;


/**
 * Class Data
 * @package kalanis\UploadPerPartes\DriveFile
 * Driver metadata about processed file
 */
final class Data implements IDriverLocation
{
    /** @var string */
    public $fileName = '';
    /** @var string */
    public $tempLocation = '';
    /** @var int<0, max> */
    public $fileSize = 0;
    /** @var int<0, max> */
    public $partsCount = 0;
    /** @var int<0, max> */
    public $bytesPerPart = 0;
    /** @var int<0, max> */
    public $lastKnownPart = 0;
    /** @var string */
    public $targetPath = '';
    /** @var string */
    public $driverName = '';

    public static function init(): self
    {
        return new static();
    }

    /**
     * @param string $fileName
     * @param string $tempLocation
     * @param int<0, max> $fileSize
     * @param int<0, max> $partsCount
     * @param int<0, max> $bytesPerPart
     * @param int<0, max> $lastKnownPart
     * @param string $targetPath
     * @param string $driverName
     * @return $this
     */
    public function setData(
        string $fileName,
        string $tempLocation,
        int $fileSize,
        int $partsCount = 0,
        int $bytesPerPart = 0,
        int $lastKnownPart = 0,
        string $targetPath = '',
        string $driverName = ''
    ): self
    {
        $this->fileName = $fileName; // final file path
        $this->tempLocation = $tempLocation; // path to temp file
        $this->fileSize = $fileSize; // final size
        $this->partsCount = $partsCount; // is on parts...
        $this->bytesPerPart = $bytesPerPart; // how long is single part
        $this->lastKnownPart = $lastKnownPart; // how many parts has been obtained
        // just for name find
        $this->targetPath = $targetPath;
        $this->driverName = $driverName;
        return $this;
    }

    public function sanitizeData(): self
    {
        $this->fileName = strval($this->fileName);
        $this->tempLocation = strval($this->tempLocation);
        $this->fileSize = intval($this->fileSize);
        $this->partsCount = intval($this->partsCount);
        $this->bytesPerPart = intval($this->bytesPerPart);
        $this->lastKnownPart = intval($this->lastKnownPart);
        $this->targetPath = strval($this->targetPath);
        $this->driverName = strval($this->driverName);
        return $this;
    }

    public function getDriverPrefix(): string
    {
        return strval($this->targetPath);
    }

    public function getDriverKey(): string
    {
        return strval($this->driverName);
    }
}
