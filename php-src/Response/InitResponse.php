<?php

namespace UploadPerPartes\Response;

use UploadPerPartes\DriveFile;
use Exception;

/**
 * Class InitResponse
 * @package UploadPerPartes\Response
 * Responses sent during upload initialization
 */
class InitResponse extends AResponse
{
    /** @var null|DriveFile\Data */
    protected $data = null;

    public static function initBegin(string $sharedKey, DriveFile\Data $data): InitResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_BEGIN);
    }

    public static function initContinue(string $sharedKey, DriveFile\Data $data): InitResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_CONTINUE);
    }

    public static function initError(string $sharedKey, DriveFile\Data $data, Exception $ex): InitResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_FAIL, $ex->getMessage());
    }

    public static function initContinueFail(string $sharedKey, DriveFile\Data $data, string $message): InitResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_FAILED_CONTINUE, $message);
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
            "name" => (string)$this->data->fileName,
            "driver" => (string)$this->sharedKey,
            "totalParts" => (int)$this->data->partsCount,
            "lastKnownPart" => (int)$this->data->lastKnownPart,
            "partSize" => (int)$this->data->bytesPerPart,
            "status" => (string)$this->status,
            "errorMessage" => (string)$this->errorMessage,
        ];
    }
}