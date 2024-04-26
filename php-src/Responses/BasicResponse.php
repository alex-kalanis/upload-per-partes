<?php

namespace kalanis\UploadPerPartes\Responses;


/**
 * Class BasicResponse
 * @package kalanis\UploadPerPartes\Responses
 * Responses from server to client
 */
class BasicResponse
{
    public const STATUS_OK = 'OK';
    public const STATUS_FAIL = 'FAIL';

    public string $serverKey = '';
    public string $status = self::STATUS_OK;
    public string $errorMessage = self::STATUS_OK;
    public string $roundaboutClient = '';

    public function setBasics(string $serverKey, string $roundaboutClient): self
    {
        $this->serverKey = $serverKey;
        $this->roundaboutClient = $roundaboutClient;
        return $this;
    }
}
