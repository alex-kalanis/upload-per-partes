<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class CancelResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent during upload cancelling
 */
class CancelResponse extends AResponse
{
    public static function initCancel(
        ?IUPPTranslations $lang,
        string $sharedKey,
        string $roundaboutClient = '',
        string $roundaboutServer = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($sharedKey, static::STATUS_OK, static::STATUS_OK, $roundaboutClient, $roundaboutServer);
    }

    public static function initError(
        ?IUPPTranslations $lang,
        string $sharedKey,
        Exception $ex,
        string $roundaboutClient = '',
        string $roundaboutServer = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($sharedKey, static::STATUS_FAIL, $ex->getMessage(), $roundaboutClient, $roundaboutServer);
    }

    public function setData(
        string $sharedKey,
        string $status,
        string $errorMessage = self::STATUS_OK,
        string $roundaboutClient = '',
        string $roundaboutServer = ''
    ): self
    {
        $this->sharedKey = $sharedKey;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->roundaboutClient = $roundaboutClient;
        $this->roundaboutServer = $roundaboutServer;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'sharedKey' => strval($this->sharedKey),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
            'serverData' => strval($this->roundaboutServer),
            'clientData' => strval($this->roundaboutClient),
        ];
    }
}
