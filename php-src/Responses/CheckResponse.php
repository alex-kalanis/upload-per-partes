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
    public string $method = '';

    public function setChecksum(string $method, string $checksum): self
    {
        $this->method = $method;
        $this->checksum = $checksum;
        return $this;
    }
}
