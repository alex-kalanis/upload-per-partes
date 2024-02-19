<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class DoneResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent during upload closure
 */
class DoneResponse extends AResponse
{
    public static function initDone(
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
        string $serverData,
        InfoFormat\Data $data,
        Exception $ex,
        string $roundaboutClient = ''
    ): self
    {
        $l = new static($lang);
        return $l->setData($serverData, $data, static::STATUS_FAIL, $ex->getMessage(), $roundaboutClient);
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
     * @return string
     */
    public function getTemporaryLocation(): string
    {
        return $this->getInfoData()->tempLocation;
    }

    /**
     * @throws UploadException
     * @return int
     */
    public function getSize(): int
    {
        return $this->getInfoData()->fileSize;
    }

    /**
     * @throws UploadException
     * @return string
     */
    public function getFileName(): string
    {
        return $this->getInfoData()->fileName;
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
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
            'clientData' => strval($this->roundaboutClient),
        ];
    }
}
