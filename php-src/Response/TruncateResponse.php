<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;
use kalanis\UploadPerPartes\InfoFormat;


/**
 * Class TruncateResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent during file truncation
 */
class TruncateResponse extends AResponse
{
    /** @var null|InfoFormat\Data */
    protected $data = null;

    public static function initOk(string $sharedKey, InfoFormat\Data $data): self
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_OK);
    }

    public static function initError(string $sharedKey, InfoFormat\Data $data, Exception $ex): self
    {
        $l = new static();
        return $l->setData($sharedKey, $data, static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, InfoFormat\Data $data, string $status, string $errorMessage = self::STATUS_OK): self
    {
        $this->sharedKey = $sharedKey;
        $this->data = $data;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'sharedKey' => strval($this->sharedKey),
            'lastKnownPart' => intval($this->data->lastKnownPart),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
        ];
    }
}
