<?php

namespace UploadPerPartes;

/**
 * Class Upload
 * @package UploadPerPartes
 * Main server library for drive upload per-partes
 */
class Upload
{
    const FILE_DRIVER_SUFF = '.partial';
    const FILE_UPLOAD_SUFF = '.upload';
    const FILE_SUFF_SEP = '.';
    const FILE_VER_SEP = '_';

    /** @var string */
    protected $targetPath = '/';
    /** @var string */
    protected $sharedKey = '';
    /** @var DriveFile */
    protected $driver = null;
    /** @var Translations */
    protected $lang = null;
    /** @var int */
    protected $bytesPerPart = 262144; // 1024*256

    /**
     * @param string $targetPath
     * @param string|null $sharedKey
     * @throws Exceptions\UploadException
     */
    public function __construct(string $targetPath, ?string $sharedKey = null)
    {
        $this->targetPath = $targetPath;
        $this->lang = $this->getTranslation();
        if ($sharedKey) {
            $this->sharedKey = $sharedKey;
            $this->initDriver($sharedKey);
        }
    }

    protected function getTranslation(): Translations
    {
        return new Translations();
    }

    /**
     * Upload file by parts, final status
     * @return Response\CancelResponse
     */
    public function partesCancel(): Response\CancelResponse
    {
        try {
            $data = $this->driver->read();
            if (! @unlink($this->targetPath . $data->tempName)) {
                throw new Exceptions\UploadException($this->lang->cannotRemoveData());
            }
            $this->driver->remove();
            return Response\CancelResponse::initCancel($this->sharedKey);
        } catch (Exceptions\UploadException $ex) {
            return Response\CancelResponse::initError($this->sharedKey, $ex);
        }
    }

    /**
     * Upload file by parts, final status
     * @return Response\DoneResponse
     */
    public function partesDone(): Response\DoneResponse
    {
        try {
            $data = $this->driver->read();
            $this->driver->remove();
            return Response\DoneResponse::initDone($this->sharedKey, $this->targetPath, $data);
        } catch (Exceptions\UploadException $ex) {
            return Response\DoneResponse::initError($this->sharedKey, DriveFile\Data::init(), $ex);
        }
    }

    /**
     * Upload file by parts, use driving file
     * @param string $content binary content
     * @param int|null $segment where it save
     * @return Response\AResponse
     */
    public function partesUpload(string $content, ?int $segment = null): Response\AResponse
    {
        try {
            $data = $this->driver->read();

            if (!is_numeric($segment)) {
                $segment = $data->lastKnownPart + 1;
                $this->saveFilePart($data, $content);
                $this->driver->updateLastPart($segment);
            } else {
                if ($segment > $data->lastKnownPart + 1) {
                    throw new Exceptions\UploadException($this->lang->readTooEarly());
                }
                $this->saveFilePart($data, $content, $segment * $data->bytesPerPart);
            }

            if ($data->lastKnownPart + 1 >= $data->partsCount) {
                return Response\UploadResponse::initComplete($this->sharedKey);
            }
            return Response\UploadResponse::initOK($this->sharedKey);

        } catch (Exceptions\UploadException $e) {
            return Response\UploadResponse::initError($this->sharedKey, $e);
        }
    }

    /**
     * Delete problematic segments
     * @param int $segment
     * @return Response\AResponse
     */
    public function partesTruncateFrom(int $segment): Response\AResponse
    {
        try {
            $this->partesTruncateFromPart($segment);
            return Response\TruncateResponse::initOK($this->sharedKey);
        } catch (Exceptions\UploadException $e) {
            return Response\TruncateResponse::initError($this->sharedKey, $e);
        }
    }

    /**
     * Check already uploaded parts
     * @param int $segment
     * @return Response\AResponse
     */
    public function partesCheck(int $segment): Response\AResponse
    {
        try {
            return Response\CheckResponse::initOK($this->sharedKey, $this->partesChecksumPart($segment));
        } catch (Exceptions\UploadException $e) {
            return Response\CheckResponse::initError($this->sharedKey, $e);
        }
    }

    /**
     * Upload file by parts, create driving file
     * @param string $remoteFileName posted file name
     * @param int $length complete file size
     * @return Response\AResponse
     */
    public function partesInit(string $remoteFileName, int $length): Response\AResponse
    {
        $fileName = $this->findName($remoteFileName);
        $sharedKey = $this->getSharedName($fileName);
        $tempName = $this->getTempFileName($fileName);
        $partsCounter = (int)($length / $this->bytesPerPart);
        $partsCounter = (($length % $this->bytesPerPart) == 0) ? (int)$partsCounter : (int)($partsCounter + 1);
        try {
            $this->initDriver($sharedKey);
            $dataPack = DriveFile\Data::init()->setData($fileName, $tempName, $length, $partsCounter, $this->bytesPerPart);
            $this->driver->create($dataPack);
            return Response\InitResponse::initBegin($sharedKey, $dataPack);

        } catch (Exceptions\ContinuityUploadException $e) { // navazani na predchozi?
            return $this->partesContinuity($fileName, $tempName, $length, $partsCounter);

        } catch (Exceptions\UploadException $e) { // obecne neco spatne
            return Response\InitResponse::initError($sharedKey, DriveFile\Data::init()->setData(
                $fileName, $tempName, $length, $partsCounter, $this->bytesPerPart, 0
            ), $e);
        }
    }

    /**
     * Find non-existing name
     * @param string $name
     * @return string
     */
    protected function findName(string $name): string
    {
        $name = $this->canonize($name);
        $suffix = $this->fileSuffix($name);
        $fileBase = $this->fileBase($name);
        if (is_file($this->targetPath . $name) && !is_file($this->targetPath . $name . static::FILE_DRIVER_SUFF)) {
            $i = 0;
            while (
            is_file($this->targetPath . $fileBase . static::FILE_VER_SEP . $i . static::FILE_SUFF_SEP . $suffix)
            ) {
                $i++;
            }
            return $fileBase . static::FILE_VER_SEP . $i . static::FILE_SUFF_SEP . $suffix;
        } else {
            return $name;
        }
    }

    protected function canonize(string $fileName): string
    {
        $f = preg_replace('/((&[[:alpha:]]{1,6};)|(&#[[:alnum:]]{1,7};))/', '', $fileName);
        $f = preg_replace('#[^[:alnum:]_\s\-\.]#', '', $f); // remove non-alnum + dots
        $f = preg_replace('#[\s]#', '_', $f); // whitespaces to underscore
        $fileSuffix = $this->fileSuffix($f);
        $fileBase = $this->fileBase($f);
        $nameLength = mb_strlen($fileSuffix);
        if (!$nameLength) {
            return mb_substr($fileBase, 0, 127); // win...
        }
        $c = mb_substr($fileBase, 0, (127 - $nameLength));
        return $c . static::FILE_SUFF_SEP . $fileSuffix;
    }

    protected function fileSuffix(string $fileName): string
    {
        $pos = mb_strrpos($fileName, static::FILE_SUFF_SEP);
        return ((false !== $pos) ? (
        (0 < $pos) ? mb_substr($fileName, $pos + 1) : ''
        ) : '');
    }

    protected function fileBase(string $fileName): string
    {
        $pos = mb_strrpos($fileName, static::FILE_SUFF_SEP);
        return ((false !== $pos) ? (
        (0 < $pos) ? mb_substr($fileName, 0, $pos) : mb_substr($fileName, 1)
        ) : $fileName);
    }

    /**
     * Upload - continue from previously scrapped run; return info to manage steps on user side
     * @param string $fileName
     * @param string $tempName
     * @param int $length
     * @param int $partsCounter
     * @return Response\AResponse
     */
    protected function partesContinuity(string $fileName, string $tempName, int $length, int $partsCounter): Response\AResponse
    {
        $sharedKey = $this->getSharedName($fileName);
        try {
            $data = $this->driver->read();
            if ($length != $data->fileSize) { // rozdilna velikost
                $data->fileName = $fileName; // failed name
                return Response\InitResponse::initContinueFail(
                    $sharedKey,
                    $data,
                    $this->lang->differentFileSizes()
                );
            }
            return Response\InitResponse::initContinue($sharedKey, $data);

        } catch (Exceptions\UploadException $e) { // chcip na pokracovani
            return Response\InitResponse::initError($sharedKey, DriveFile\Data::init()->setData(
                $fileName, $tempName, $length, $partsCounter, $this->bytesPerPart, 0
            ), $e);
        }
    }

    /**
     * @param string $sharedKey
     * @throws Exceptions\UploadException
     */
    protected function initDriver(string $sharedKey)
    {
        $this->driver = new DriveFile($this->lang, DriveFile\ADriveFile::init(
            $this->lang,
            $this->getDriverVariant(),
            $this->targetPath . $sharedKey
        ));
    }

    protected function getDriverVariant(): int
    {
        return DriveFile\ADriveFile::VARIANT_TEXT;
    }

    protected function getSharedName(string $fileName): string
    {
        return $this->fileBase($fileName) . static::FILE_DRIVER_SUFF;
    }

    protected function getTempFileName(string $fileName): string
    {
        return $this->fileBase($fileName) . static::FILE_UPLOAD_SUFF;
    }

    /**
     * @param DriveFile\Data $data
     * @param string $content binary content
     * @param int|null $seek where it save
     * @return bool
     * @throws Exceptions\UploadException
     */
    protected function saveFilePart(DriveFile\Data $data, string $content, ?int $seek = null)
    {
        if (is_numeric($seek)) {
            $pointer = fopen($this->targetPath . $data->tempName, 'wb');
            if (false === $pointer) {
                throw new Exceptions\UploadException($this->lang->cannotOpenFile());
            }
            $position = fseek($pointer, $seek);
            if ($position == -1) {
                throw new Exceptions\UploadException($this->lang->cannotSeekFile());
            }
            if (false === fwrite($pointer, $content, strlen($content))) {
                throw new Exceptions\UploadException($this->lang->cannotWriteFile());
            }
            fclose($pointer);
        } else {
            $pointer = fopen($this->targetPath . $data->tempName, 'ab');
            if (false == $pointer) {
                throw new Exceptions\UploadException($this->lang->cannotOpenFile());
            }
            if (false === fwrite($pointer, $content, strlen($content))) {
                throw new Exceptions\UploadException($this->lang->cannotWriteFile());
            }
            fclose($pointer);
        }
        return true;
    }

    /**
     * @param int $segment where it will be
     * @return string
     * @throws Exceptions\UploadException
     */
    protected function partesChecksumPart(int $segment): string
    {
        $data = $this->driver->read();
        $this->checkSegment($data, $segment);

        return md5(file_get_contents(
            $this->targetPath . $data->tempName,
            false,
            null,
            $data->bytesPerPart * $segment,
            $data->bytesPerPart
        ));
    }

    /**
     * @param int $segment where it will be
     * @return void
     * @throws Exceptions\UploadException
     */
    protected function partesTruncateFromPart(int $segment): void
    {
        $data = $this->driver->read();
        $this->checkSegment($data, $segment);

        $handle = fopen($this->targetPath . $data->tempName, 'r+');
        if (!ftruncate($handle, $data->bytesPerPart * $segment)) {
            fclose($handle);
            throw new Exceptions\UploadException($this->lang->cannotTruncateFile());
        }
        rewind($handle);
        fclose($handle);
    }

    /**
     * @param DriveFile\Data $data
     * @param int $segment
     * @throws Exceptions\UploadException
     */
    protected function checkSegment(DriveFile\Data $data, int $segment): void
    {
        if ($segment < 0) {
            throw new Exceptions\UploadException($this->lang->segmentOutOfBounds());
        }
        if ($segment > $data->partsCount) {
            throw new Exceptions\UploadException($this->lang->segmentOutOfBounds());
        }
        if ($segment > $data->lastKnownPart) {
            throw new Exceptions\UploadException($this->lang->segmentNotUploadedYet());
        }
    }
}