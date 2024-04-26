<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders;


use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class Text
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Driver file - format plaintext
 */
class Text extends AEncoder
{
    const DATA_SEPARATOR = ':';
    const LINE_SEPARATOR = "\r\n";

    public function unpack(string $content): Data
    {
        $lines = explode(static::LINE_SEPARATOR, $content);
        $libData = new Data();
        if (false !== $lines) {
            foreach ($lines as $line) {
                if (0 < mb_strlen($line)) {
                    $data = explode(static::DATA_SEPARATOR, $line);
                    if (false !== $data) {
                        list($key, $value, $nothing) = $data;
                        $libData->{$key} = $value;
                    }
                }
            }
        }
        return $libData->clear();
    }

    public function pack(Data $data): string
    {
        $dataArray = (array) $data;
        $dataLines = [];
        foreach ($dataArray as $key => $value) {
            $dataLines[] = implode(static::DATA_SEPARATOR, [$key, $value, '']);
        }
        return implode(static::LINE_SEPARATOR, $dataLines);
    }
}
