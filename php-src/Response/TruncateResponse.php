<?php

namespace UploadPerPartes\Response;

use Exception;
use UploadPerPartes\DataFormat;

/**
 * Class TruncateResponse
 * @package UploadPerPartes\Response
 * Responses sent during file truncation
 */
class TruncateResponse extends AResponse
{
    /** @var null|DataFormat\Data */
    protected $data = null;

    public static function initOk(string $sharedKey, DataFormat\Data $data): TruncateResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, DataFormat\Data $data, Exception $ex): TruncateResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, DataFormat\Data $data, string $status, string $errorMessage = self::STATUS_OK)
    {
        $this->sharedKey = $sharedKey;
        $this->data = $data;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            "sharedKey" => (string)$this->sharedKey,
            "lastKnownPart" => (int)$this->data->lastKnownPart,
            "status" => (string)$this->status,
            "errorMessage" => (string)$this->errorMessage,
        ];
    }
}