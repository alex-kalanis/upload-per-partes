<?php

namespace UploadPerPartes\examples;

use UploadPerPartes;

/**
 * Class Upload
 * @package UploadPerPartes\examples
 * Library for uploading files per partes - port into our project
 */
class Uploader extends \UploadPerPartes\Uploader
{
    protected $bytesPerPart = 10485760; // segment size: 1024*1024*10 = 10MB

    protected function getSharedKey(string $fileName): string
    {
        return md5(\Lib_String::generateRandomText(256));
    }

    protected function getTempFileName(string $fileName): string
    {
        return \Lib_String::generateRandomText(32) . static::FILE_UPLOAD_SUFF;
    }
}