<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class InitResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent during upload initialization
 */
class InitResponse extends AResponse
{
    public static function initOk(
        ?IUPPTranslations $lang,
        string $sharedKey,
        InfoFormat\Data $data,
        string $roundaboutClient = '',
        string $roundaboutServer = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($sharedKey, $data, static::STATUS_OK, static::STATUS_OK, $roundaboutClient, $roundaboutServer);
    }

    public static function initError(
        ?IUPPTranslations $lang,
        InfoFormat\Data $data,
        Exception $ex,
        string $roundaboutClient = '',
        string $roundaboutServer = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData('', $data, static::STATUS_FAIL, $ex->getMessage(), $roundaboutClient, $roundaboutServer);
    }

    public function setData(
        string $sharedKey,
        InfoFormat\Data $data,
        string $status,
        string $errorMessage = self::STATUS_OK,
        string $roundaboutClient = '',
        string $roundaboutServer = ''
    ): self
    {
        $this->sharedKey = $sharedKey;
        $this->setInfoData($data);
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->roundaboutClient = $roundaboutClient;
        $this->roundaboutServer = $roundaboutServer;
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
            'name' => strval($this->getInfoData()->fileName),
            'sharedKey' => strval($this->sharedKey),
            'totalParts' => intval($this->getInfoData()->partsCount),
            'lastKnownPart' => intval($this->getInfoData()->lastKnownPart),
            'partSize' => intval($this->getInfoData()->bytesPerPart),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
            'serverData' => strval($this->roundaboutServer),
            'clientData' => strval($this->roundaboutClient),
        ];
    }
}
