<?php

namespace kalanis\UploadPerPartes\ServerData\DataModifiers;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;
use kalanis\UploadPerPartes\Uploader\RandomStrings;


/**
 * Class Line
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Driver file - format string line
 */
class Line extends ServerData\AModifiers implements
    Interfaces\IInfoFormatting,
    Interfaces\InfoStorage\ForFiles,
    Interfaces\InfoStorage\ForKV,
    Interfaces\InfoStorage\ForPass,
    Interfaces\InfoStorage\ForStorage,
    Interfaces\InfoStorage\ForVolume
{
    const DATA_SEPARATOR = '|';

    public function fromFormat(string $content): ServerData\Data
    {
        $line = explode(static::DATA_SEPARATOR, $content);
        $libData = new ServerData\Data();
        $libData->remoteName = strval($line[1]); // final file path
        $libData->tempDir = strval($line[3]); // path to temp file
        $libData->fileSize = max(0, intval($line[5])); // final size
        $libData->partsCount = max(0, intval($line[7])); // is on parts...
        $libData->bytesPerPart = max(0, intval($line[9])); // how long is single part
        $libData->lastKnownPart = max(0, intval($line[11])); // how many parts has been obtained
        // just for name find
        $libData->targetDir = strval($line[13]);
        $libData->finalName = strval($line[15]);

        return $libData->sanitizeData();
    }

    public function toFormat(ServerData\Data $data): string
    {
        return implode(static::DATA_SEPARATOR, [
            RandomStrings::randomLength(),
            $data->remoteName,
            RandomStrings::randomLength(),
            $data->tempDir,
            RandomStrings::randomLength(),
            $data->fileSize,
            RandomStrings::randomLength(),
            $data->partsCount,
            RandomStrings::randomLength(),
            $data->bytesPerPart,
            RandomStrings::randomLength(),
            $data->lastKnownPart,
            RandomStrings::randomLength(),
            $data->targetDir,
            RandomStrings::randomLength(),
            $data->finalName,
            RandomStrings::randomLength(),
        ]);
    }
}
