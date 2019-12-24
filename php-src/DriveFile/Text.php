<?php

namespace UploadPerPartes\DriveFile;

use UploadPerPartes\Exceptions\UploadException;

/**
 * Class Text
 * @package UploadPerPartes\DriveFile
 * Processing driver file - variant plaintext
 */
class Text extends ADriveFile
{
    const DATA_SEPARATOR = ':';
    const LINE_SEPARATOR = "\r\n";

    public function load(): Data
    {
        $content = file($this->path);
        if (false === $content) {
            throw new UploadException($this->lang->driveFileCannotRead());
        }
        $libData = new Data;
        foreach ($content as $line) {
            if (mb_strlen($line) > 0) {
                list($key, $value, $nothing) = explode(static::DATA_SEPARATOR, $line);
                $libData->{$key} = $value;
            }
        }
        return $libData;
    }

    public function save(Data $data): void
    {
        $dataArray = (array)$data;
        $dataLines = [];
        foreach ($dataArray as $key => $value) {
            $dataLines[] = implode(static::DATA_SEPARATOR, [$key, $value, '']);
        }
        if (false === file_put_contents($this->path, implode(static::LINE_SEPARATOR, $dataLines))) {
            throw new UploadException($this->lang->driveFileCannotWrite());
        }
    }
}