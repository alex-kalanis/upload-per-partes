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
        string $serverData,
        InfoFormat\Data $data,
        string $roundaboutClient = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($serverData, $data, static::STATUS_OK, static::STATUS_OK, $roundaboutClient);
    }

    public static function initError(
        ?IUPPTranslations $lang,
        InfoFormat\Data $data,
        Exception $ex,
        string $roundaboutClient = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData('', $data, static::STATUS_FAIL, $ex->getMessage(), $roundaboutClient);
    }

    public function setData(
        string $serverData,
        InfoFormat\Data $data,
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
            'name' => strval($this->getInfoData()->fileName),
            'serverData' => strval($this->serverData),
            'totalParts' => intval($this->getInfoData()->partsCount),
            'lastKnownPart' => intval($this->getInfoData()->lastKnownPart),
            'partSize' => intval($this->getInfoData()->bytesPerPart),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
            'clientData' => strval($this->roundaboutClient),
        ];
    }
}
