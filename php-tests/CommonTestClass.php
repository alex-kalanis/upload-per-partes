<?php

use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\Target;
use kalanis\UploadPerPartes\Uploader;
use kalanis\UploadPerPartes\UploadException;


class CommonTestClass extends \PHPUnit\Framework\TestCase
{
    protected function mockTestFile(): string
    {
        return $this->getTestDir() . 'testing.upload';
    }

    protected function mockSharedKey(): string
    {
        return 'driver.partial';
    }

    protected function getTestDir(): string
    {
        return realpath(__DIR__ . '/tmp/') . '/';
    }

    protected function getTestFile(): string
    {
        return realpath(__DIR__ . '/testing-ipsum.txt');
    }

    protected function mockData(): Uploader\Data
    {
        return (new Uploader\Data())->setData(
            '/tmp/',
            'fghjkl.partial',
            $this->getTestDir() . 'abcdef',
            'abcdef',
            123456,
            12,
            64,
            7
        );
    }
}


class XTrans implements Interfaces\IUppTranslations
{
    public function uppBadResponse(string $responseType): string
    {
        return 'mock';
    }

    public function uppTargetNotSet(): string
    {
        return 'mock';
    }

    public function uppTargetIsWrong(string $url): string
    {
        return 'mock';
    }

    public function uppChecksumVariantIsWrong(string $variant): string
    {
        return 'mock';
    }

    public function uppDecoderVariantIsWrong(string $variant): string
    {
        return 'mock';
    }

    public function uppIncomingDataCannotDecode(): string
    {
        return 'mock';
    }

    public function uppSentNameIsEmpty(): string
    {
        return 'mock';
    }

    public function uppChecksumIsEmpty(): string
    {
        return 'mock';
    }

    public function uppDataEncoderVariantNotSet(): string
    {
        return 'mock';
    }

    public function uppDataEncoderVariantIsWrong(string $variant): string
    {
        return 'mock';
    }

    public function uppDataModifierVariantNotSet(): string
    {
        return 'mock';
    }

    public function uppDataModifierVariantIsWrong(string $variant): string
    {
        return 'mock';
    }

    public function uppKeyEncoderVariantNotSet(): string
    {
        return 'mock';
    }

    public function uppKeyEncoderVariantIsWrong(string $className): string
    {
        return 'mock';
    }

    public function uppKeyModifierVariantNotSet(): string
    {
        return 'mock';
    }

    public function uppKeyModifierVariantIsWrong(string $className): string
    {
        return 'mock';
    }

    public function uppDriveFileStorageNotSet(): string
    {
        return 'mock';
    }

    public function uppDriveFileCannotRead(string $key): string
    {
        return 'mock';
    }

    public function uppDriveFileCannotWrite(string $key): string
    {
        return 'mock';
    }

    public function uppDriveFileAlreadyExists(string $driveFile): string
    {
        return 'mock';
    }

    public function uppTempEncoderVariantNotSet(): string
    {
        return 'mock';
    }

    public function uppTempEncoderVariantIsWrong(string $variant): string
    {
        return 'mock';
    }

    public function uppTempStorageNotSet(): string
    {
        return 'mock';
    }

    public function uppFinalEncoderVariantNotSet(): string
    {
        return 'mock';
    }

    public function uppFinalEncoderVariantIsWrong(string $variant): string
    {
        return 'mock';
    }

    public function uppFinalStorageNotSet(): string
    {
        return 'mock';
    }

    public function uppCannotReadFile(string $location): string
    {
        return 'mock';
    }

    public function uppCannotWriteFile(string $location): string
    {
        return 'mock';
    }

    public function uppDriveFileCannotRemove(string $key): string
    {
        return 'mock';
    }

    public function uppCannotRemoveData(string $location): string
    {
        return 'mock';
    }

    public function uppCannotTruncateFile(string $location): string
    {
        return 'mock';
    }

    public function uppSegmentOutOfBounds(int $segment): string
    {
        return 'mock';
    }
}


class XFailOper implements Interfaces\IOperations
{
    public function init(string $targetPath, string $targetFileName, int $length, string $clientData = '̈́'): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function check(string $serverData, int $segment, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function truncate(string $serverData, int $segment, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function upload(string $serverData, string $content, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function done(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function cancel(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }
}


class XFailDrivingDataEncoder extends Target\Local\DrivingFile\DataEncoders\AEncoder
{
    public function pack(Uploader\Data $data): string
    {
        throw new UploadException('mock');
    }

    public function unpack(string $data): Uploader\Data
    {
        throw new UploadException('mock');
    }
}


class XFailDrivingDataModifier extends Target\Local\DrivingFile\DataModifiers\AModifier
{
    public function pack(string $data): string
    {
        throw new UploadException('mock');
    }

    public function unpack(string $data): string
    {
        throw new UploadException('mock');
    }
}


class XFailDrivingKeyEncoder extends Target\Local\DrivingFile\KeyEncoders\AEncoder
{
    public function encode(Uploader\Data $data): string
    {
        throw new UploadException('mock');
    }
}


class XFailDrivingKeyModifier extends Target\Local\DrivingFile\KeyModifiers\AModifier
{
    public function pack(string $data): string
    {
        throw new UploadException('mock');
    }

    public function unpack(string $data): string
    {
        throw new UploadException('mock');
    }
}


class XFailTempEncoder extends Target\Local\TemporaryStorage\KeyEncoders\AEncoder
{
    public function toPath(Uploader\Data $data): string
    {
        throw new UploadException('mock');
    }
}


class XFailFinalEncoder extends Target\Local\FinalStorage\KeyEncoders\AEncoder
{
    public function toPath(Uploader\Data $data): string
    {
        throw new UploadException('mock');
    }
}


class XFailChecksum implements Interfaces\IChecksum
{
    public function getMethod(): string
    {
        return 'failed one';
    }

    public function checksum(string $data): string
    {
        throw new UploadException('mock');
    }
}

class XFailDecoder implements Interfaces\IContentDecoder
{
    public function getMethod(): string
    {
        return 'failed one';
    }

    public function decode(string $data): string
    {
        throw new UploadException('mock');
    }
}
