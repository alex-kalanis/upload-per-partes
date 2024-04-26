<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders;


use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class Json
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Driver file - format JSON
 */
class Json extends AEncoder
{
    public function unpack(string $content): Data
    {
        $libData = new Data();
        $jsonData = @json_decode($content, true);
        if (is_iterable($jsonData)) {
            foreach ($jsonData as $key => $value) {
                $libData->{$key} = $value;
            }
        }
        return $libData->clear();
    }

    public function pack(Data $data): string
    {
        return strval(json_encode($data));
    }
}
