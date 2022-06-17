<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;


/**
 * Class CheckResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent from content check
 */
class CheckResponse extends AResponse
{
    /** @var string */
    protected $checksum = '';

    public static function initOk(string $sharedKey, string $checksum): self
    {
        $l = new static();
        return $l->setData($sharedKey, $checksum, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, Exception $ex): self
    {
        $l = new static();
        return $l->setData($sharedKey, '', static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, string $checksum, string $status, string $errorMessage = self::STATUS_OK): self
    {
        $this->sharedKey = $sharedKey;
        $this->checksum = $checksum;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'sharedKey' => strval($this->sharedKey),
            'checksum' => strval($this->checksum),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
        ];
    }
}
