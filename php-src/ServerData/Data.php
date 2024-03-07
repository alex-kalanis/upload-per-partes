<?php

namespace kalanis\UploadPerPartes\ServerData;


/**
 * Class Data
 * @package kalanis\UploadPerPartes\ServerData
 * Driver metadata about processed file
 */
final class Data
{
    /** @var string */
    public $remoteName = '';
    /** @var string */
    public $tempDir = '';
    /** @var string */
    public $tempName = '';
    /** @var string */
    public $targetDir = '';
    /** @var string */
    public $finalName = '';
    /** @var int<0, max> */
    public $fileSize = 0;
    /** @var int<0, max> */
    public $partsCount = 0;
    /** @var int<0, max> */
    public $bytesPerPart = 0;
    /** @var int<0, max> */
    public $lastKnownPart = 0;

    public static function init(): self
    {
        return new static();
    }

    /**
     * @param string $remoteFileName
     * @param string $tempDir
     * @param string $tempName
     * @param string $targetDir
     * @param string $finalName
     * @param int<0, max> $fileSize
     * @param int<0, max> $partsCount
     * @param int<0, max> $bytesPerPart
     * @param int<0, max> $lastKnownPart
     * @return $this
     */
    public function setData(
        string $remoteFileName,
        string $tempDir,
        string $tempName,
        string $targetDir,
        string $finalName,
        int $fileSize,
        int $partsCount = 0,
        int $bytesPerPart = 0,
        int $lastKnownPart = 0
    ): self
    {
        $this->remoteName = $remoteFileName; // what name is passed from client
        $this->tempDir = $tempDir; // where it is during upload
        $this->tempName = $tempName; // what it is during upload
        $this->targetDir = $targetDir; // where it will be stored
        $this->finalName = $finalName; // what name it will have after upload
        $this->fileSize = $fileSize; // final size
        $this->partsCount = $partsCount; // is on parts...
        $this->bytesPerPart = $bytesPerPart; // how long is single part
        $this->lastKnownPart = $lastKnownPart; // how many parts has been obtained
        return $this;
    }

    public function sanitizeData(): self
    {
        $this->remoteName = strval($this->remoteName);
        $this->tempDir = strval($this->tempDir);
        $this->tempName = strval($this->tempName);
        $this->targetDir = strval($this->targetDir);
        $this->finalName = strval($this->finalName);
        $this->fileSize = intval($this->fileSize);
        $this->partsCount = intval($this->partsCount);
        $this->bytesPerPart = intval($this->bytesPerPart);
        $this->lastKnownPart = intval($this->lastKnownPart);
        return $this;
    }
}
