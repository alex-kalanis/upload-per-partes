<?php

namespace kalanis\UploadPerPartes\Uploader;


/**
 * Class DataPack
 * @package kalanis\UploadPerPartes\Uploader
 * Calculations hashes, need for checking content
 * Actions over DataStorage
 */
class DataPack
{
    protected Data $data;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    public function create(string $targetDir, string $remoteFileName, int $fileSize): Data
    {
        $pack = clone $this->data;
        $pack->targetDir = $targetDir;
        $pack->targetName = $remoteFileName;
        $pack->fileSize = max(0, $fileSize);
        return $pack;
    }

    public function fillSizes(Data $data, int $partsCount, int $bytesPerPart, int $lastKnownPart): Data
    {
        $data->partsCount = max(0, $partsCount);
        $data->bytesPerPart = max(0, $bytesPerPart);
        $data->lastKnownPart = max(0, $lastKnownPart);
        return $data;
    }

    public function fillTempData(Data $data, Config $config): Data
    {
        $data->tempDir = $config->tempDir;
        return $data;
    }

    public function nextSegment(Data $data): int
    {
        return $data->lastKnownPart + 1;
    }

    public function lastKnown(Data $data, int $segment): Data
    {
        $data->lastKnownPart = max(0, $segment);
        return $data;
    }

    public function getFinalName(Data $data): string
    {
        return $data->targetName;
    }
}
