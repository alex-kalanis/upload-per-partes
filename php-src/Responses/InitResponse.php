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
    public string $encoder = '';
    public string $check = '';

    public function setInitData(
        Data $data,
        string $encoder,
        string $check
    ): self
    {
        $this->name = $data->targetName;
        $this->totalParts = $data->partsCount;
        $this->lastKnownPart = $data->lastKnownPart;
        $this->partSize = $data->bytesPerPart;
        $this->encoder = $encoder;
        $this->check = $check;
        return $this;
    }

    public function setPassedInitData(
        string $targetName,
        int $partsCount,
        int $lastKnownPart,
        int $bytesPerPart,
        string $encoder,
        string $check
    ): self
    {
        $this->name = $targetName;
        $this->totalParts = $partsCount;
        $this->lastKnownPart = $lastKnownPart;
        $this->partSize = $bytesPerPart;
        $this->encoder = $encoder;
        $this->check = $check;
        return $this;
    }
}
