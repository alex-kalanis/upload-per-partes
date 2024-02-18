<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class CheckResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent from content check
 */
class CheckResponse extends AResponse
{
    /** @var string */
    protected $checksum = '';

    public static function initOk(
        ?IUPPTranslations $lang,
        string $sharedKey,
        string $checksum,
        string $roundaboutClient = '',
        string $roundaboutServer = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($sharedKey, $checksum, static::STATUS_OK, static::STATUS_OK, $roundaboutClient, $roundaboutServer);
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
        return $l->setData($sharedKey, '', static::STATUS_FAIL, $ex->getMessage(), $roundaboutClient, $roundaboutServer);
    }

    public function setData(
        string $sharedKey,
        string $checksum,
        string $status,
        string $errorMessage = self::STATUS_OK,
        string $roundaboutClient = '',
        string $roundaboutServer = ''
    ): self
    {
        $this->sharedKey = $sharedKey;
        $this->checksum = $checksum;
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
            'checksum' => strval($this->checksum),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
            'serverData' => strval($this->roundaboutServer),
            'clientData' => strval($this->roundaboutClient),
        ];
    }
}
