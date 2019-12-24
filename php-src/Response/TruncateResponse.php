<?php

namespace UploadPerPartes\Response;

use Exception;

/**
 * Class TruncateResponse
 * @package UploadPerPartes\Response
 * Responses sent during file truncation
 */
class TruncateResponse extends AResponse
{
    const STATUS_OK = 'OK';
    const STATUS_FAIL = 'FAIL';

    protected $sharedKey = '';
    protected $errorMessage = self::STATUS_OK;
    protected $status = self::STATUS_OK;

    public static function initOk(string $sharedKey): TruncateResponse
    {
        $l = new static();
        return $l->setData($sharedKey, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, Exception $ex): TruncateResponse
    {
        $l = new static();
        return $l->setData($sharedKey, static::STATUS_FAIL, $ex->getMessage());
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
            "driver" => (string)$this->sharedKey,
            "status" => (string)$this->status,
            "errorMessage" => (string)$this->errorMessage,
        ];
    }
}