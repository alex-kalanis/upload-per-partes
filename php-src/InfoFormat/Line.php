<?php

namespace kalanis\UploadPerPartes\InfoFormat;


use kalanis\UploadPerPartes\Interfaces\IInfoFormatting;


/**
 * Class Line
 * @package kalanis\UploadPerPartes\DriveFile
 * Driver file - format string line
 */
class Line implements IInfoFormatting
{
    const DATA_SEPARATOR = '|';

    public function fromFormat(string $content): Data
    {
        $line = explode(static::DATA_SEPARATOR, $content);
        $libData = new Data();
        $libData->fileName = $line[1]; // final file path
        $libData->tempLocation = $line[3]; // path to temp file
        $libData->fileSize = $line[5]; // final size
        $libData->partsCount = $line[7]; // is on parts...
        $libData->bytesPerPart = $line[9]; // how long is single part
        $libData->lastKnownPart = $line[11]; // how many parts has been obtained
        // just for name find
        $libData->targetPath = $line[13];
        $libData->driverName = $line[15];

        return $libData->sanitizeData();
    }

    public function toFormat(Data $data): string
    {
        return implode(static::DATA_SEPARATOR, [
            $this->randStr(),
            $data->fileName,
            $this->randStr(),
            $data->tempLocation,
            $this->randStr(),
            $data->fileSize,
            $this->randStr(),
            $data->partsCount,
            $this->randStr(),
            $data->bytesPerPart,
            $this->randStr(),
            $data->lastKnownPart,
            $this->randStr(),
            $data->targetPath,
            $this->randStr(),
            $data->driverName,
            $this->randStr(),
        ]);
    }

    protected function randStr(): string
    {
        $which = md5(rand());
        return substr($which, 0, octdec(substr($which, -1)));
    }
}
