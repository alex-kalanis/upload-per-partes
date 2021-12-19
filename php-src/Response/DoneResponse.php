<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;
use kalanis\UploadPerPartes\InfoFormat;


/**
 * Class DoneResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent during upload closure
 */
class DoneResponse extends AResponse
{
    /** @var null|InfoFormat\Data */
    protected $data = null;

    public static function initDone(string $sharedKey, InfoFormat\Data $data): DoneResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, InfoFormat\Data $data, Exception $ex): DoneResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, InfoFormat\Data $data, string $status, string $errorMessage = self::STATUS_OK)
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

    public function getSize(): int
    {
        return $this->data->fileSize;
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
