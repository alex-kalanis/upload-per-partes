<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders;


use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\Uploader\RandomStrings;


/**
 * Class Line
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Driver file - format string line
 */
class Line extends AEncoder
{
    private const DATA_SEPARATOR = '|';

    public function unpack(string $content): Data
    {
        $line = explode(self::DATA_SEPARATOR, $content);
        $libData = new Data();
        $libData->tempDir = strval($line[1]); // path to temp file
        $libData->tempName = strval($line[3]); // final file path
        $libData->targetDir = strval($line[5]);
        $libData->targetName = strval($line[7]);
        $libData->fileSize = max(0, intval($line[9])); // final size
        $libData->partsCount = max(0, intval($line[11])); // is on parts...
        $libData->bytesPerPart = max(0, intval($line[13])); // how long is single part
        $libData->lastKnownPart = max(0, intval($line[15])); // how many parts has been obtained

        return $libData->clear();
    }

    public function pack(Data $data): string
    {
        return implode(self::DATA_SEPARATOR, [
            RandomStrings::randomLength(),
            $data->tempDir,
            RandomStrings::randomLength(),
            $data->tempName,
            RandomStrings::randomLength(),
            $data->targetDir,
            RandomStrings::randomLength(),
            $data->targetName,
            RandomStrings::randomLength(),
            $data->fileSize,
            RandomStrings::randomLength(),
            $data->partsCount,
            RandomStrings::randomLength(),
            $data->bytesPerPart,
            RandomStrings::randomLength(),
            $data->lastKnownPart,
            RandomStrings::randomLength(),
        ]);
    }
}
