<?php

namespace BasicTests;

use CommonTestClass;
use Support;
use UploadPerPartes\DataStorage;
use UploadPerPartes\InfoFormat;
use UploadPerPartes\Uploader\DriveFile;
use UploadPerPartes\Uploader\Translations;

class ADriveFile extends CommonTestClass
{
    public function tearDown(): void
    {
        $driveFile = $this->getDriveFile();
        if ($driveFile->exists($this->mockKey())) {
            $driveFile->remove($this->mockKey());
        }
        parent::tearDown();
    }

    protected function mockKey(): string
    {
        return 'fghjkl' . DataStorage\TargetSearch::FILE_DRIVER_SUFF;
    }

    protected function getDriveFile(): DriveFile
    {
        $lang = Translations::init();
        $storage = new Support\InfoRam($lang);
        $target = new DataStorage\TargetSearch($lang);
        $key = new Support\Key($lang, $target);
        $format = new InfoFormat\Json();
        return new DriveFile($lang, $storage, $format, $key);
    }
}