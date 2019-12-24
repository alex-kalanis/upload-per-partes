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
    const STATUS_OK = 'OK';
    const STATUS_FAIL = 'FAIL';
    const STATUS_COMPLETE = 'COMPLETE';

    protected $sharedKey = '';
    protected $errorMessage = self::STATUS_OK;
    protected $status = self::STATUS_OK;

    public static function initOK(string $sharedKey): UploadResponse
    {
        $l = new static();
        return $l->setData($sharedKey, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, Exception $ex): UploadResponse
    {
        $l = new static();
        return $l->setData($sharedKey, static::STATUS_FAIL, $ex->getMessage());
    }

    public static function initComplete(string $sharedKey): UploadResponse
    {
        $l = new static();
        return $l->setData($sharedKey, static::STATUS_COMPLETE);
    }

    public function setData(string $sharedKey, string $status, string $errorMessage = self::STATUS_OK)
    {
        $this->sharedKey = $sharedKey;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            "driver" => $this->sharedKey,
            "status" => $this->status,
            "errorMessage" => $this->errorMessage,
        ];
    }
}