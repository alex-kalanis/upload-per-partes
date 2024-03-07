<?php

namespace kalanis\UploadPerPartes\examples;


use kalanis\UploadPerPartes;


/**
 * Class Upload
 * @package kalanis\UploadPerPartes\examples
 * Library for uploading files per partes - port into our project
 */
class Uploader extends UploadPerPartes\Uploader
{
    protected function getCalc($params): UploadPerPartes\Uploader\CalculateSizes
    {
        return parent::getCalc(['calculator' => 10485760]); // segment size: 1024*1024*10 = 10MB
    }
}
