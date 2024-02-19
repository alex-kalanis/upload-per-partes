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
        string $serverData,
        string $roundaboutClient = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($serverData, static::STATUS_OK, static::STATUS_OK, $roundaboutClient);
    }

    public static function initError(
        ?IUPPTranslations $lang,
        string $serverData,
        Exception $ex,
        string $roundaboutClient = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($serverData, static::STATUS_FAIL, $ex->getMessage(), $roundaboutClient);
    }

    public function setData(
        string $serverData,
        string $status,
        string $errorMessage = self::STATUS_OK,
        string $roundaboutClient = ''
    ): self
    {
        $this->serverData = $serverData;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->roundaboutClient = $roundaboutClient;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'serverData' => strval($this->serverData),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
            'clientData' => strval($this->roundaboutClient),
        ];
    }
}
