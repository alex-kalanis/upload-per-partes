<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class Translations
 * @package kalanis\UploadPerPartes
 * Return translated quotes from backend
 * - necessary due many translation systems through web
 * For work extends this class and pass extension into your project
 */
class Translations implements IUPPTranslations
{
    public function uppSentNameIsEmpty(): string
    {
        return 'SENT FILE NAME IS EMPTY';
    }

    public function uppUploadNameIsEmpty(): string
    {
        return 'UPLOAD FILE NAME IS EMPTY';
    }

    public function uppSharedKeyIsEmpty(): string
    {
        return 'SHARED KEY IS EMPTY';
    }

    public function uppSharedKeyIsInvalid(): string
    {
        return 'SHARED KEY IS INVALID';
    }

    public function uppKeyVariantNotSet(): string
    {
        return 'KEY VARIANT NOT SET';
    }

    public function uppKeyVariantIsWrong(string $className): string
    {
        return 'KEY VARIANT IS WRONG';
    }

    public function uppTargetDirIsEmpty(): string
    {
        return 'TARGET DIR IS NOT SET';
    }

    public function uppDriveFileAlreadyExists(string $driveFile): string
    {
        return 'DRIVEFILE ALREADY EXISTS';
    }

    public function uppDriveFileNotContinuous(string $driveFile): string
    {
        return 'DRIVEFILE IS NOT CONTINUOUS';
    }

    public function uppDriveFileCannotRemove(string $key): string
    {
        return 'DRIVEFILE CANNOT BE REMOVED';
    }

    public function uppDriveFileVariantNotSet(): string
    {
        return 'DRIVEFILE VARIANT NOT SET';
    }

    public function uppDriveDataNotSet(): string
    {
        return 'DRIVE DATA NOT SET';
    }

    public function uppDriveFileVariantIsWrong(string $className): string
    {
        return 'DRIVEFILE VARIANT IS WRONG';
    }

    public function uppDriveFileCannotRead(string $key): string
    {
        return 'CANNOT READ DRIVEFILE';
    }

    public function uppDriveFileCannotWrite(string $key): string
    {
        return 'CANNOT WRITE DRIVEFILE';
    }

    public function uppCannotRemoveData(string $location): string
    {
        return 'CANNOT REMOVE DATA';
    }

    public function uppReadTooEarly(string $key): string
    {
        return 'READ TOO EARLY';
    }

    public function uppCannotOpenFile(string $location): string
    {
        return 'CANNOT OPEN FILE';
    }

    /**
     * @param string $location
     * @return string
     * @codeCoverageIgnore
     * @see \kalanis\UploadPerPartes\DataStorage\VolumeBasic::getPart
     */
    public function uppCannotReadFile(string $location): string
    {
        return 'CANNOT READ FILE';
    }

    /**
     * @param string $location
     * @return string
     * @codeCoverageIgnore   no ideas how to fail seek
     * @see \kalanis\UploadPerPartes\DataStorage\VolumeBasic::addPart
     */
    public function uppCannotSeekFile(string $location): string
    {
        return 'CANNOT SEEK FILE';
    }

    /**
     * @param string $location
     * @return string
     * @codeCoverageIgnore
     * @see \kalanis\UploadPerPartes\DataStorage\VolumeBasic::addPart
     */
    public function uppCannotWriteFile(string $location): string
    {
        return 'CANNOT WRITE FILE';
    }

    /**
     * @param string $location
     * @return string
     * @codeCoverageIgnore
     * @see \kalanis\UploadPerPartes\DataStorage\VolumeBasic::truncate
     */
    public function uppCannotTruncateFile(string $location): string
    {
        return 'FILE CANNOT TRUNCATE';
    }

    public function uppSegmentOutOfBounds(int $segment): string
    {
        return 'SEGMENT OUT OF BOUNDS';
    }

    public function uppSegmentNotUploadedYet(int $segment): string
    {
        return 'SEGMENT NOT UPLOADED YET';
    }
}
