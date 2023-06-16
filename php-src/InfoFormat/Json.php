<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Interfaces\IInfoFormatting;


/**
 * Class Json
 * @package kalanis\UploadPerPartes\DataFormat
 * Driver file - format JSON
 */
class Json implements IInfoFormatting
{
    public function fromFormat(string $content): Data
    {
        $libData = new Data();
        $jsonData = json_decode($content, true);
        if (is_iterable($jsonData)) {
            foreach ($jsonData as $key => $value) {
                $libData->{$key} = $value;
            }
        }
        return $libData->sanitizeData();
    }

    public function toFormat(Data $data): string
    {
        return strval(json_encode($data));
    }
}
