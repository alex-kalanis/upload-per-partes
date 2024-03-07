<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;


/**
 * Class Text
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Driver file - format plaintext
 */
class Text extends ServerData\AModifiers implements
    Interfaces\IInfoFormatting,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForPass,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume
{
    const DATA_SEPARATOR = ':';
    const LINE_SEPARATOR = "\r\n";

    public function fromFormat(string $content): ServerData\Data
    {
        $lines = explode(static::LINE_SEPARATOR, $content);
        $libData = new ServerData\Data();
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

    public function toFormat(ServerData\Data $data): string
    {
        $dataArray = (array) $data;
        $dataLines = [];
        foreach ($dataArray as $key => $value) {
            $dataLines[] = implode(static::DATA_SEPARATOR, [$key, $value, '']);
        }
        return implode(static::LINE_SEPARATOR, $dataLines);
    }
}
