<?php

namespace UploadPerPartes\examples;

use UploadPerPartes;

/**
 * Class Upload
 * @package UploadPerPartes\examples
 * Library for uploading files per partes - port into our project
 */
class Upload extends \UploadPerPartes\Upload
{
    protected $bytesPerPart = 10485760; // segment size: 1024*1024*10 = 10MB

    public function __construct(string $targetPath, ?string $sharedKey = null)
    {
        parent::__construct($targetPath, $sharedKey);
        if (!is_file($this->targetPath)) {
            \Lib_Other::mkdir($this->targetPath);
        }
    }

    protected function initDriver(string $sharedKey)
    {
        $this->driver = new UploadPerPartes\DriveFile($this->lang, new DriveRedis(
            $this->lang,
            $sharedKey,
            new \Rc()
        ));
    }

    protected function getSharedKey(string $fileName): string
    {
        return md5(\Lib_String::generateRandomText(256));
    }

    protected function getTempFileName(string $fileName): string
    {
        return \Lib_String::generateRandomText(32) . static::FILE_UPLOAD_SUFF;
    }
}