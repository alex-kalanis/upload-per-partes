<?php

namespace BasicTests;

use CommonTestClass;
use UploadPerPartes\DataFormat;
use UploadPerPartes\Storage;
use UploadPerPartes\Uploader\DriveFile;
use UploadPerPartes\Uploader\Translations;

class ADriveFile extends CommonTestClass
{
    public function tearDown()
    {
        $driveFile = $this->getDriveFile();
        if ($driveFile->exists($this->mockKey())) {
            $driveFile->remove($this->mockKey());
        }
        parent::tearDown();
    }

    protected function mockKey(): string
    {
        return 'fghjkl' . Storage\TargetSearch::FILE_DRIVER_SUFF;
    }

    protected function getDriveFile(): DriveFile
    {
        $lang = Translations::init();
        $storage = new Ram($lang);
        $target = new Storage\TargetSearch($lang);
        $key = new Key($lang, $target);
        $format = new DataFormat\Json();
        return new DriveFile($lang, $storage, $format, $key);
    }
}