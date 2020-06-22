<?php

namespace UploadPerPartes\examples;

use UploadPerPartes;
use UploadPerPartes\Uploader\Calculates;

/**
 * Class Upload
 * @package UploadPerPartes\examples
 * Library for uploading files per partes - port into our project
 */
class Uploader extends \UploadPerPartes\Uploader
{
    protected function getCalc(): Calculates
    {
        return new Calculates(10485760); // segment size: 1024*1024*10 = 10MB
    }
}