<?php

namespace kalanis\UploadPerPartes\Responses;


use kalanis\UploadPerPartes\UploadException;

/**
 * Class ErrorResponse
 * @package kalanis\UploadPerPartes\Responses
 * Error happens - set different variables
 */
class ErrorResponse extends BasicResponse
{
    public string $status = self::STATUS_FAIL;

    public function setError(UploadException $ex): self
    {
        $this->setErrorMessage($ex->getMessage());
        return $this;
    }

    public function setErrorMessage(string $message): self
    {
        $this->errorMessage = $message;
        return $this;
    }
}
