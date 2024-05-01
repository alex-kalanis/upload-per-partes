<?php

namespace kalanis\UploadPerPartes\Responses;


/**
 * Class LastKnownResponse
 * @package kalanis\UploadPerPartes\Responses
 * Responses from server to client
 */
class LastKnownResponse extends BasicResponse
{
    public int $lastKnownPart = 0;

    public function setLastKnown(int $lastKnownPart): self
    {
        $this->lastKnownPart = $lastKnownPart;
        return $this;
    }
}
