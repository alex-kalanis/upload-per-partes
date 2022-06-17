<?php

namespace kalanis\UploadPerPartes\InfoFormat;


/**
 * Class Text
 * @package kalanis\UploadPerPartes\DriveFile
 * Driver file - format plaintext
 */
class Text extends AFormat
{
    const DATA_SEPARATOR = ':';
    const LINE_SEPARATOR = "\r\n";

    public function fromFormat(string $content): Data
    {
        $lines = explode(static::LINE_SEPARATOR, $content);
        $libData = new Data();
        foreach ($lines as $line) {
            if (0 < mb_strlen($line)) {
                list($key, $value, $nothing) = explode(static::DATA_SEPARATOR, $line);
                $libData->{$key} = $value;
            }
        }
        return $libData->sanitizeData();
    }

    public function toFormat(Data $data): string
    {
        $dataArray = (array) $data;
        $dataLines = [];
        foreach ($dataArray as $key => $value) {
            $dataLines[] = implode(static::DATA_SEPARATOR, [$key, $value, '']);
        }
        return implode(static::LINE_SEPARATOR, $dataLines);
    }
}
