<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;


/**
 * Class CancelResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent during upload cancelling
 */
class CancelResponse extends AResponse
{
    public static function initCancel(string $sharedKey): self
    {
        $l = new static();
        return $l->setData($sharedKey, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, Exception $ex): self
    {
        $l = new static();
        return $l->setData($sharedKey, static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, string $status, string $errorMessage = self::STATUS_OK): self
    {
        $this->sharedKey = $sharedKey;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'sharedKey' => strval($this->sharedKey),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
        ];
    }
}
