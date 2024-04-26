<?php

namespace kalanis\UploadPerPartes\Responses;


/**
 * Class CheckResponse
 * @package kalanis\UploadPerPartes\Responses
 * Responses from server to client
 */
class CheckResponse extends BasicResponse
{
    public string $checksum = '';

    public function setChecksum(string $checksum): self
    {
        $this->checksum = $checksum;
        return $this;
    }
}
