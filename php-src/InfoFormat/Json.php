<?php

namespace kalanis\UploadPerPartes\InfoFormat;


/**
 * Class Json
 * @package kalanis\UploadPerPartes\DataFormat
 * Driver file - format JSON
 */
class Json extends AFormat
{
    public function fromFormat(string $content): Data
    {
        $libData = new Data();
        $jsonData = json_decode($content, true);
        foreach ($jsonData as $key => $value) {
            $libData->{$key} = $value;
        }
        return $libData->sanitizeData();
    }

    public function toFormat(Data $data): string
    {
        return strval(json_encode($data));
    }
}
