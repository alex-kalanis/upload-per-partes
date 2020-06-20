<?php

namespace UploadPerPartes;

use UploadPerPartes\Uploader\Translations;

/**
 * Class Helper
 * @package UploadPerPartes
 * Main server library for drive upload per-partes
 */
class Uploader
{
    /** @var Keys\AKey */
    protected $key = null;
    /** @var Uploader\Processor */
    protected $processor = null;
    /** @var int */
    protected $bytesPerPart = 262144; // 1024*256

    /**
     * @throws Exceptions\UploadException
     */
    public function __construct()
    {
        $lang = new Uploader\Translations();
        $storage = new Storage\Volume($lang);
        $format = DataFormat\AFormat::getFormat($lang, DataFormat\AFormat::FORMAT_JSON);
        $this->key = $this->getKey($lang);
        $driver = new Uploader\DriveFile($lang, $storage, $format, $this->key);
        $this->processor = $this->getProcessor($lang, $driver);
    }

    protected function getKey(Translations $lang): Keys\AKey
    {
        $keys = new Keys\Volume($lang);
        return $keys->setTargetDir('/');
    }

    protected function getProcessor(Translations $lang, Uploader\DriveFile $driver): Uploader\Processor
    {
        return new Uploader\Processor($lang, $driver);
    }

    /**
     * Upload file by parts, final status
     * @param string $sharedKey
     * @return Response\AResponse
     */
    public function cancel(string $sharedKey): Response\AResponse
    {
        try {
            $this->processor->cancel($sharedKey);
            return Response\CancelResponse::initCancel($sharedKey);
        } catch (Exceptions\UploadException $ex) {
            return Response\CancelResponse::initError($sharedKey, $ex);
        }
    }

    /**
     * Upload file by parts, final status
     * @param string $sharedKey
     * @return Response\AResponse
     */
    public function done(string $sharedKey): Response\AResponse
    {
        try {
            return Response\DoneResponse::initDone($sharedKey, $this->processor->done($sharedKey));
        } catch (Exceptions\UploadException $ex) {
            return Response\DoneResponse::initError($sharedKey, DataFormat\Data::init(), $ex);
        }
    }

    /**
     * Upload file by parts, use driving file
     * @param string $sharedKey
     * @param string $content binary content
     * @param int|null $segment where it save
     * @return Response\AResponse
     */
    public function upload(string $sharedKey, string $content, ?int $segment = null): Response\AResponse
    {
        try {
            return Response\UploadResponse::initOK($sharedKey, $this->processor->upload($sharedKey, $content, $segment));
        } catch (Exceptions\UploadException $e) {
            return Response\UploadResponse::initError($sharedKey, DataFormat\Data::init(), $e);
        }
    }

    /**
     * Delete problematic segments
     * @param string $sharedKey
     * @param int $segment
     * @return Response\AResponse
     */
    public function truncateFrom(string $sharedKey, int $segment): Response\AResponse
    {
        try {
            return Response\TruncateResponse::initOK($sharedKey, $this->processor->truncateFrom($sharedKey, $segment));
        } catch (Exceptions\UploadException $e) {
            return Response\TruncateResponse::initError($sharedKey, DataFormat\Data::init(), $e);
        }
    }

    /**
     * Check already uploaded parts
     * @param string $sharedKey
     * @param int $segment
     * @return Response\AResponse
     */
    public function check(string $sharedKey, int $segment): Response\AResponse
    {
        try {
            return Response\CheckResponse::initOK($sharedKey, $this->processor->check($sharedKey, $segment));
        } catch (Exceptions\UploadException $e) {
            return Response\CheckResponse::initError($sharedKey, $e);
        }
    }

    /**
     * Upload file by parts, create driving file
     * @param string $targetPath
     * @param string $remoteFileName posted file name
     * @param int $length complete file size
     * @return Response\AResponse
     */
    public function init(string $targetPath, string $remoteFileName, int $length): Response\AResponse
    {
        $partsCounter = $this->calcParts($length);
        try {
            $this->key->setTargetDir($targetPath)->setRemoteFileName($remoteFileName)->process();
            $sharedKey = $this->key->getNewSharedKey();
            $dataPack = DataFormat\Data::init()->setData($this->key->getFileName(), $this->key->getTargetLocation(), $length, $partsCounter, $this->bytesPerPart);
            return Response\InitResponse::initOk($sharedKey, $this->processor->init($dataPack, $sharedKey));

        } catch (Exceptions\UploadException $e) { // obecne neco spatne
            return Response\InitResponse::initError(DataFormat\Data::init()->setData(
                $remoteFileName, '', $length, $partsCounter, $this->bytesPerPart, 0
            ), $e);
        }
    }

    protected function calcParts(int $length): int
    {
        $partsCounter = (int)($length / $this->bytesPerPart);
        return (($length % $this->bytesPerPart) == 0) ? (int)$partsCounter : (int)($partsCounter + 1);
    }
}
