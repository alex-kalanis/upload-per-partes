<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;


/**
 * Class Serialize
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Driver file - format into serialized string
 */
class Serialize extends ServerData\AModifiers implements
    Interfaces\IInfoFormatting,
    Interfaces\ILimitPassedData,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForPass,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume
{
    public function fromFormat(string $content): ServerData\Data
    {
        $data = @unserialize($content, [
            'allowed_classes' => [ServerData\Data::class],
            'max_depth' => 2,
        ]);
        if (false === $data) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        if (!$data instanceof ServerData\Data) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        return $data->sanitizeData();
    }

    public function toFormat(ServerData\Data $data): string
    {
        return $this->getLimitedData($data);
    }

    public function getLimitedData(ServerData\Data $data): string
    {
        return strval(serialize($data));
    }
}
