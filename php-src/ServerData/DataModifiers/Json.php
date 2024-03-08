<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;


/**
 * Class Json
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Driver file - format JSON
 */
class Json extends ServerData\AModifiers implements
    Interfaces\IInfoFormatting,
    Interfaces\ILimitDataInternalKey,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForPass,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume
{
    public function fromFormat(string $content): ServerData\Data
    {
        $libData = new ServerData\Data();
        $jsonData = @json_decode($content, true);
        if (is_iterable($jsonData)) {
            foreach ($jsonData as $key => $value) {
                $libData->{$key} = $value;
            }
        }
        return $libData->sanitizeData();
    }

    public function toFormat(ServerData\Data $data): string
    {
        return $this->getLimitedData($data);
    }

    public function getLimitedData(ServerData\Data $data): string
    {
        return strval(json_encode($data));
    }
}
