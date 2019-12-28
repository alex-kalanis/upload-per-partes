<?php

namespace UploadPerPartes\DriveFile;

use UploadPerPartes\Exceptions\UploadException;

/**
 * Class Json
 * @package UploadPerPartes\DriveFile
 * Processing driver file - variant JSON
 */
class Json extends ADriveFile
{
    public function load(): Data
    {
        $content = file_get_contents($this->path);
        if (false === $content) {
            throw new UploadException($this->lang->driveFileCannotRead());
        }
        $libData = new Data;
        $jsonData = json_decode($content, true);
        foreach ($jsonData as $key => $value) {
            $libData->{$key} = $value;
        }
        return $libData->sanitizeData();
    }

    public function save(Data $data): void
    {
        if (false === file_put_contents($this->path, json_encode($data))) {
            throw new UploadException($this->lang->driveFileCannotWrite());
        }
    }
}