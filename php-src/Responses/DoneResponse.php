<?php

namespace kalanis\UploadPerPartes\Responses;


/**
 * Class DoneResponse
 * @package kalanis\UploadPerPartes\Responses
 * Responses from server to client
 */
class DoneResponse extends BasicResponse
{
    public string $name = '';

    public function setFinalName(string $finalName): self
    {
        $this->name = $finalName;
        return $this;
    }
}
