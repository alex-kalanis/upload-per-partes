<?php

namespace UploadPerPartes\Response;

use Exception;
use UploadPerPartes\DataFormat;

/**
 * Class DoneResponse
 * @package UploadPerPartes\Response
 * Responses sent during upload closure
 */
class DoneResponse extends AResponse
{
    /** @var null|DataFormat\Data */
    protected $data = null;

    public static function initDone(string $sharedKey, DataFormat\Data $data): DoneResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, DataFormat\Data $data, Exception $ex): DoneResponse
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

    public function getTemporaryLocation(): string
    {
        return $this->data->tempLocation;
    }

    public function getFileName(): string
    {
        return $this->data->fileName;
    }

    public function jsonSerialize()
    {
        return [
            "name" => (string)$this->data->fileName,
            "sharedKey" => (string)$this->sharedKey,
            "status" => (string)$this->status,
            "errorMessage" => (string)$this->errorMessage,
        ];
    }
}