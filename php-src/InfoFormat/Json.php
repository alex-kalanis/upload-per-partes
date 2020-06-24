<?php

namespace UploadPerPartes\InfoFormat;

/**
 * Class Json
 * @package UploadPerPartes\DataFormat
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
        return (string)json_encode($data);
    }
}