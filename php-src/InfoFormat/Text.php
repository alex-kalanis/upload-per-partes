<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Interfaces\IInfoFormatting;


/**
 * Class Text
 * @package kalanis\UploadPerPartes\DriveFile
 * Driver file - format plaintext
 */
class Text implements IInfoFormatting
{
    const DATA_SEPARATOR = ':';
    const LINE_SEPARATOR = "\r\n";

    public function fromFormat(string $content): Data
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
