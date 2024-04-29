<?php

namespace kalanis\UploadPerPartes\Interfaces;


/**
 * Interface IUppTranslations
 * @package kalanis\UploadPerPartes\Interfaces
 * Return translated quotes from backend
 * - necessary due many translation systems through web
 * For work extends this class and pass extension into your project
 */
interface IUppTranslations
{
    public function uppBadResponse(string $responseType): string;

    public function uppTargetNotSet(): string;

    public function uppTargetIsWrong(string $url): string;

    public function uppChecksumVariantIsWrong(string $variant): string;

    public function uppDecoderVariantIsWrong(string $variant): string;

    public function uppIncomingDataCannotDecode(): string;

    public function uppSentNameIsEmpty(): string;

    public function uppChecksumIsEmpty(): string;

    public function uppDataEncoderVariantNotSet(): string;

    public function uppDataEncoderVariantIsWrong(string $variant): string;

    public function uppDataModifierVariantNotSet(): string;

    public function uppDataModifierVariantIsWrong(string $variant): string;

    public function uppKeyEncoderVariantNotSet(): string;

    public function uppKeyEncoderVariantIsWrong(string $className): string;

    public function uppKeyModifierVariantNotSet(): string;

    public function uppKeyModifierVariantIsWrong(string $className): string;

    public function uppDriveFileStorageNotSet(): string;

    public function uppDriveFileCannotRead(string $key): string;

    public function uppDriveFileCannotWrite(string $key): string;

    public function uppDriveFileAlreadyExists(string $driveFile): string;

    public function uppTempEncoderVariantNotSet(): string;

    public function uppTempEncoderVariantIsWrong(string $variant): string;

    public function uppTempStorageNotSet(): string;

    public function uppFinalEncoderVariantNotSet(): string;

    public function uppFinalEncoderVariantIsWrong(string $variant): string;

    public function uppFinalStorageNotSet(): string;

    public function uppCannotReadFile(string $location): string;

    public function uppCannotWriteFile(string $location): string;

    public function uppDriveFileCannotRemove(string $key): string;

    public function uppCannotRemoveData(string $location): string;

    public function uppCannotTruncateFile(string $location): string;

    public function uppSegmentOutOfBounds(int $segment): string;
}