<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;


/**
 * Class CheckResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent from content check
 */
class CheckResponse extends AResponse
{
    protected $checksum = '';

    public static function initOk(string $sharedKey, string $checksum): CheckResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $checksum, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, Exception $ex): CheckResponse
    {
        $l = new static();
        return $l->setData($sharedKey, '', static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, string $checksum, string $status, string $errorMessage = self::STATUS_OK)
    {
        $this->sharedKey = $sharedKey;
        $this->checksum = $checksum;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            "sharedKey" => (string)$this->sharedKey,
            "checksum" => (string)$this->checksum,
            "status" => (string)$this->status,
            "errorMessage" => (string)$this->errorMessage,
        ];
    }
}
