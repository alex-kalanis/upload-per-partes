<?php

namespace kalanis\UploadPerPartes\Interfaces;


/**
 * Interface IUPPTranslate
 * @package kalanis\UploadPerPartes\Interfaces
 * Return translated quotes from backend
 * - necessary due many translation systems through web
 * For work extends this class and pass extension into your project
 */
interface IUPPTranslations
{
    public function uppSentNameIsEmpty(): string;

    public function uppUploadNameIsEmpty(): string;

    public function uppSharedKeyIsEmpty(): string;

    public function uppSharedKeyIsInvalid(): string;

    public function uppKeyVariantNotSet(): string;

    public function uppKeyVariantIsWrong(string $className): string;

    public function uppTargetDirIsEmpty(): string;

    public function uppDriveFileAlreadyExists(string $driveFile): string;

    public function uppDriveFileNotContinuous(string $driveFile): string;

    public function uppDriveFileCannotRemove(string $key): string;

    public function uppDriveFileVariantNotSet(): string;

    public function uppDriveFileVariantIsWrong(string $className): string;

    public function uppDriveFileCannotRead(string $key): string;

    public function uppDriveFileCannotWrite(string $key): string;

    public function uppCannotRemoveData(string $location): string;

    public function uppReadTooEarly(string $key): string;

    public function uppCannotOpenFile(string $location): string;

    public function uppCannotReadFile(string $location): string;

    public function uppCannotSeekFile(string $location): string;

    public function uppCannotWriteFile(string $location): string;

    public function uppCannotTruncateFile(string $location): string;

    public function uppSegmentOutOfBounds(int $segment): string;

    public function uppSegmentNotUploadedYet(int $segment): string;
}