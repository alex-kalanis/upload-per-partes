<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\Interfaces\IUppTranslations;


/**
 * Class Translations
 * @package kalanis\UploadPerPartes
 * Return translated quotes from backend
 * - necessary due many translation systems through web
 * For work extends this class and pass extension into your project
 */
class Translations implements IUppTranslations
{
    public function uppBadResponse(string $responseType): string
    {
        return 'Selected bad response type.';
    }

    public function uppTargetNotSet(): string
    {
        return 'The target is not set.';
    }

    public function uppTargetIsWrong(string $url): string
    {
        return sprintf('The target is set in a wrong way. Cannot determine it. *%s*', $url);
    }

    public function uppChecksumVariantIsWrong(string $variant): string
    {
        return sprintf('The checksum is set in a wrong way. Cannot determine it. *%s*', $variant);
    }

    public function uppDecoderVariantIsWrong(string $variant): string
    {
        return sprintf('The decoder is set in a wrong way. Cannot determine it. *%s*', $variant);
    }

    public function uppIncomingDataCannotDecode(): string
    {
        return 'Cannot decode incoming data!';
    }

    public function uppSentNameIsEmpty(): string
    {
        return 'Sent file name is empty.';
    }

    public function uppChecksumIsEmpty(): string
    {
        return 'There is no data for checksum on storage.';
    }

    public function uppDataEncoderVariantNotSet(): string
    {
        return 'The driving data encoder variant is not set!';
    }

    public function uppDataEncoderVariantIsWrong(string $variant): string
    {
        return sprintf('The driving data encoder is set in a wrong way. Cannot determine it. *%s*', $variant);
    }

    public function uppDataModifierVariantNotSet(): string
    {
        return 'The driving data modifier variant is not set!';
    }

    public function uppDataModifierVariantIsWrong(string $variant): string
    {
        return sprintf('The driving data modifier is set in a wrong way. Cannot determine it. *%s*', $variant);
    }

    public function uppKeyEncoderVariantNotSet(): string
    {
        return 'The driving data key encoder variant is not set!';
    }

    public function uppKeyEncoderVariantIsWrong(string $className): string
    {
        return sprintf('The driving data key encoder variant is set in a wrong way. Cannot determine it. *%s*', $className);
    }

    public function uppKeyModifierVariantNotSet(): string
    {
        return 'The driving data key modifier variant is not set!';
    }

    public function uppKeyModifierVariantIsWrong(string $className): string
    {
        return sprintf('The driving data key modifier variant is set in a wrong way. Cannot determine it. *%s*', $className);
    }

    public function uppDriveFileStorageNotSet(): string
    {
        return 'The driving file storage is not set correctly!';
    }

    public function uppDriveFileCannotRead(string $key): string
    {
        return sprintf('Cannot read *%s* driving file from its storage.', $key);
    }

    public function uppDriveFileCannotWrite(string $key): string
    {
        return sprintf('Cannot write *%s* driving file into its storage.', $key);
    }

    public function uppDriveFileAlreadyExists(string $driveFile): string
    {
        return sprintf('The driving file *%s* already exists in storage.', $driveFile);
    }

    public function uppTempEncoderVariantNotSet(): string
    {
        return 'The temporary storage encoder variant is not set!';
    }

    public function uppTempEncoderVariantIsWrong(string $variant): string
    {
        return sprintf('The temporary storage encoder variant is set in a wrong way. Cannot determine it. *%s*', $variant);
    }

    public function uppTempStorageNotSet(): string
    {
        return 'The temporary storage is not set correctly!';
    }

    public function uppFinalEncoderVariantNotSet(): string
    {
        return 'The final storage encoder variant is not set!';
    }

    public function uppFinalEncoderVariantIsWrong(string $variant): string
    {
        return sprintf('The final storage encoder variant is set in a wrong way. Cannot determine it. *%s*', $variant);
    }

    public function uppFinalStorageNotSet(): string
    {
        return 'The final storage is not set correctly!';
    }

    public function uppCannotReadFile(string $location): string
    {
        return sprintf('Cannot read file *%s*', $location);
    }

    public function uppCannotWriteFile(string $location): string
    {
        return sprintf('Cannot write file *%s*', $location);
    }

    public function uppDriveFileCannotRemove(string $key): string
    {
        return sprintf('Cannot remove drive file for upload *%s*', $key);
    }

    public function uppCannotRemoveData(string $location): string
    {
        return sprintf('Cannot remove drive file for upload *%s*', $location);
    }

    public function uppCannotTruncateFile(string $location): string
    {
        return sprintf('Cannot truncate file *%s*', $location);
    }

    public function uppSegmentOutOfBounds(int $segment): string
    {
        return sprintf('Segment *%d* is out-of-bounds.', $segment);
    }
}
