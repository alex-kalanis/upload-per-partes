<?php

namespace UploadPerPartes\Response;

use Exception;

/**
 * Class UploadResponse
 * @package UploadPerPartes\Response
 * Responses sent during upload of file each part
 */
class UploadResponse extends AResponse
{
    /** @var null|DriveFile\Data */
    protected $data = null;

    public static function initOK(string $sharedKey, DriveFile\Data $data): UploadResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, DriveFile\Data $data, Exception $ex): UploadResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, DriveFile\Data $data, string $status, string $errorMessage = self::STATUS_OK)
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
            "sharedKey" => $this->sharedKey,
            "lastKnownPart" => (int)$this->data->lastKnownPart,
            "status" => $this->status,
            "errorMessage" => $this->errorMessage,
        ];
    }
}