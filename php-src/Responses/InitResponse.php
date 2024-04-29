<?php

namespace kalanis\UploadPerPartes\Responses;


use kalanis\UploadPerPartes\Uploader\Data;

/**
 * Class InitResponse
 * @package kalanis\UploadPerPartes\Responses
 * Responses from server to client
 */
class InitResponse extends BasicResponse
{
    public string $name = '';
    public int $totalParts = 0;
    public int $lastKnownPart = 0;
    public int $partSize = 0;
    public string $encoders = '';
    public string $check = '';

    public function setInitData(
        Data $data,
        string $encoders,
        string $check
    ): self
    {
        $this->name = $data->targetName;
        $this->totalParts = $data->partsCount;
        $this->lastKnownPart = $data->lastKnownPart;
        $this->partSize = $data->bytesPerPart;
        $this->encoders = $encoders;
        $this->check = $check;
        return $this;
    }

    public function setPassedInitData(
        string $targetName,
        int $partsCount,
        int $lastKnownPart,
        int $bytesPerPart,
        string $encoders,
        string $check
    ): self
    {
        $this->name = $targetName;
        $this->totalParts = $partsCount;
        $this->lastKnownPart = $lastKnownPart;
        $this->partSize = $bytesPerPart;
        $this->encoders = $encoders;
        $this->check = $check;
        return $this;
    }
}
