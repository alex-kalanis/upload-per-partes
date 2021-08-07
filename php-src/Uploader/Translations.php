<?php

namespace kalanis\UploadPerPartes\Uploader;


/**
 * Class Translations
 * @package kalanis\UploadPerPartes
 * Return translated quotes from backend
 * - necessary due many translation systems through web
 * For work extends this class and pass extension into your project
 */
class Translations
{
    public static function init(): Translations
    {
        return new static();
    }

    public function sentNameIsEmpty(): string
    {
        return 'SENT FILE NAME IS EMPTY';
    }

    public function uploadNameIsEmpty(): string
    {
        return 'UPLOAD FILE NAME IS EMPTY';
    }

    public function sharedKeyIsEmpty(): string
    {
        return 'SHARED KEY IS EMPTY';
    }

    public function sharedKeyIsInvalid(): string
    {
        return 'SHARED KEY IS INVALID';
    }

    public function keyVariantNotSet(): string
    {
        return 'KEY VARIANT NOT SET';
    }

    public function targetDirIsEmpty(): string
    {
        return 'TARGET DIR IS NOT SET';
    }

    public function driveFileAlreadyExists(): string
    {
        return 'DRIVEFILE ALREADY EXISTS';
    }

    public function driveFileNotContinuous(): string
    {
        return 'DRIVEFILE IS NOT CONTINUOUS';
    }

    public function driveFileCannotRemove(): string
    {
        return 'DRIVEFILE CANNOT BE REMOVED';
    }

    public function driveFileVariantNotSet(): string
    {
        return 'DRIVEFILE VARIANT NOT SET';
    }

    public function driveFileCannotRead(): string
    {
        return 'CANNOT READ DRIVEFILE';
    }

    public function driveFileCannotWrite(): string
    {
        return 'CANNOT WRITE DRIVEFILE';
    }

    public function cannotRemoveData(): string
    {
        return 'CANNOT REMOVE DATA';
    }

    public function readTooEarly(): string
    {
        return 'READ TOO EARLY';
    }

    public function cannotOpenFile(): string
    {
        return 'CANNOT OPEN FILE';
    }

    /**
     * @return string
     * @codeCoverageIgnore
     * @see \kalanis\UploadPerPartes\DataStorage\VolumeBasic::getPart
     */
    public function cannotReadFile(): string
    {
        return 'CANNOT READ FILE';
    }

    /**
     * @return string
     * @codeCoverageIgnore   no ideas how to fail seek
     * @see \kalanis\UploadPerPartes\DataStorage\VolumeBasic::addPart
     */
    public function cannotSeekFile(): string
    {
        return 'CANNOT SEEK FILE';
    }

    /**
     * @return string
     * @codeCoverageIgnore
     * @see \kalanis\UploadPerPartes\DataStorage\VolumeBasic::addPart
     */
    public function cannotWriteFile(): string
    {
        return 'CANNOT WRITE FILE';
    }

    /**
     * @return string
     * @codeCoverageIgnore
     * @see \kalanis\UploadPerPartes\DataStorage\VolumeBasic::truncate
     */
    public function cannotTruncateFile(): string
    {
        return 'FILE CANNOT TRUNCATE';
    }

    public function segmentOutOfBounds(): string
    {
        return 'SEGMENT OUT OF BOUNDS';
    }

    public function segmentNotUploadedYet(): string
    {
        return 'SEGMENT NOT UPLOADED YET';
    }
}
