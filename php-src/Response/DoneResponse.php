<?php

namespace UploadPerPartes\Response;

use UploadPerPartes\DriveFile;
use Exception;

/**
 * Class DoneResponse
 * @package UploadPerPartes\Response
 * Responses sent during upload closure
 */
class DoneResponse extends AResponse
{
    /** @var null|DriveFile\Data */
    protected $data = null;
    protected $targetPath = '';

    public static function initDone(string $sharedKey, string $targetPath, DriveFile\Data $data): DoneResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, $targetPath, static::STATUS_COMPLETE);
    }

    public static function initError(string $sharedKey, DriveFile\Data $data, Exception $ex): DoneResponse
    {
        $l = new static();
        return $l->setData($sharedKey, $data, '', static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, DriveFile\Data $data, string $targetPath, string $status, string $errorMessage = self::STATUS_OK)
    {
        $this->sharedKey = $sharedKey;
        $this->targetPath = $targetPath;
        $this->data = $data;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getTargetFile(): string
    {
        return $this->targetPath . $this->data->tempName;
    }

    public function getFileName(): string
    {
        return $this->data->fileName;
    }

    public function jsonSerialize()
    {
        return [
            "name" => (string)$this->data->fileName,
            "driver" => (string)$this->sharedKey,
            "status" => (string)$this->status,
            "errorMessage" => (string)$this->errorMessage,
        ];
    }
}