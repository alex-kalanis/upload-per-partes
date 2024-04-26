<?php

namespace kalanis\UploadPerPartes\Uploader;


/**
 * Class Data
 * @package kalanis\UploadPerPartes\Uploader
 * Driver metadata about processed file
 */
final class Data
{
    public string $tempDir = '';
    public string $tempName = '';
    public string $targetDir = '';
    public string $targetName = '';
    /** @var int<0, max> */
    public int $fileSize = 0;
    /** @var int<0, max> */
    public int $partsCount = 0;
    /** @var int<0, max> */
    public int $bytesPerPart = 0;
    /** @var int<0, max> */
    public int $lastKnownPart = 0;

    /**
     * @param string $tempDir
     * @param string $tempName
     * @param string $targetDir
     * @param string $targetName
     * @param int<0, max> $fileSize
     * @param int<0, max> $partsCount
     * @param int<0, max> $bytesPerPart
     * @param int<0, max> $lastKnownPart
     * @return $this
     */
    public function setData(
        string $tempDir,
        string $tempName,
        string $targetDir,
        string $targetName,
        int $fileSize,
        int $partsCount = 0,
        int $bytesPerPart = 0,
        int $lastKnownPart = 0
    ): self
    {
        $this->tempDir = $tempDir; // where it is during upload
        $this->tempName = $tempName; // what it is during upload
        $this->targetDir = $targetDir; // where it will be stored
        $this->targetName = $targetName; // what name it will have after upload
        $this->fileSize = max(0, $fileSize); // final size
        $this->partsCount = max(0, $partsCount); // is on parts...
        $this->bytesPerPart = max(0, $bytesPerPart); // how long is single part
        $this->lastKnownPart = max(0, $lastKnownPart); // how many parts has been obtained
        return $this;
    }

    public function clear(): self
    {
        $this->fileSize = max(0, $this->fileSize);
        $this->partsCount = max(0, $this->partsCount);
        $this->bytesPerPart = max(0, $this->bytesPerPart);
        $this->lastKnownPart = max(0, $this->lastKnownPart);
        return $this;
    }
}
