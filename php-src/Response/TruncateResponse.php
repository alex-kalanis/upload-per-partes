<?php

namespace kalanis\UploadPerPartes\Response;


use Exception;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class TruncateResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses sent during file truncation
 */
class TruncateResponse extends AResponse
{
    public static function initOk(?IUPPTranslations $lang, string $sharedKey, InfoFormat\Data $data): self
    {
        $l = new static($lang);
        return $l->setData($sharedKey, $data, static::STATUS_OK);
    }

    public static function initError(?IUPPTranslations $lang, string $sharedKey, InfoFormat\Data $data, Exception $ex): self
    {
        $l = new static($lang);
        return $l->setData($sharedKey, $data, static::STATUS_FAIL, $ex->getMessage());
    }

    public function setData(string $sharedKey, InfoFormat\Data $data, string $status, string $errorMessage = self::STATUS_OK): self
    {
        $this->sharedKey = $sharedKey;
        $this->setInfoData($data);
        $this->status = $status;
        $this->errorMessage = $errorMessage;
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
            'sharedKey' => strval($this->sharedKey),
            'lastKnownPart' => intval($this->getInfoData()->lastKnownPart),
            'status' => strval($this->status),
            'errorMessage' => strval($this->errorMessage),
        ];
    }
}
