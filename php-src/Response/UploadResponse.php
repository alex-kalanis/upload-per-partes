<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\ServerData;


/**
 * Class UploadResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent during upload of file each part
 */
class UploadResponse extends AResponse
{
    public static function initOK(
        ?IUPPTranslations $lang,
        string $serverData,
        ServerData\Data $data,
        string $roundaboutClient = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($serverData, $data, static::STATUS_OK, static::STATUS_OK, $roundaboutClient);
    }

    public static function initError(
        ?IUPPTranslations $lang,
        string $serverData,
        ServerData\Data $data,
        Exception $ex,
        string $roundaboutClient = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($serverData, $data, static::STATUS_FAIL, $ex->getMessage(), $roundaboutClient);
    }

    public function setData(
        string $serverData,
        ServerData\Data $data,
        string $status,
        string $errorMessage = self::STATUS_OK,
        string $roundaboutClient = ''
    ): self
    {
        $this->serverData = $serverData;
        $this->setInfoData($data);
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->roundaboutClient = $roundaboutClient;
        return $this;
    }

    /**
     * @throws UploadException
     * @return array<string, string|int>
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'serverData' => strval($this->serverData),
            'lastKnownPart' => intval($this->getInfoData()->lastKnownPart),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
            'clientData' => strval($this->roundaboutClient),
        ];
    }
}
