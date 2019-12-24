<?php

namespace UploadPerPartes\Response;

use Exception;

/**
 * Class CancelResponse
 * @package UploadPerPartes\Response
 * Responses sent during upload cancelling
 */
class CancelResponse extends AResponse
{
    const STATUS_OK = 'OK';
    const STATUS_FAIL = 'FAIL';

    protected $sharedKey = '';
    protected $errorMessage = self::STATUS_OK;
    protected $status = self::STATUS_OK;

    public static function initCancel(string $sharedKey): CancelResponse
    {
        $l = new static();
        return $l->setData($sharedKey, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, Exception $ex): CancelResponse
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