<?php

namespace kalanis\UploadPerPartes\Responses;


/**
 * Class LastKnownResponse
 * @package kalanis\UploadPerPartes\Responses
 * Responses from server to client
 */
class LastKnownResponse extends BasicResponse
{
    public int $lastKnown = 0;

    public function setLastKnown(int $lastKnown): self
    {
        $this->lastKnown = $lastKnown;
        return $this;
    }
}
