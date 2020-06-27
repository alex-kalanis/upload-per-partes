<?php

namespace UploadPerPartes;

use UploadPerPartes\Uploader\Calculates;
use UploadPerPartes\Uploader\Hashed;
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
    /** @var InfoStorage\AStorage */
    protected $infoStorage = null;
    /** @var DataStorage\AStorage */
    protected $dataStorage = null;
    /** @var DataStorage\TargetSearch */
    protected $targetSearch = null;
    /** @var Calculates */
    protected $calculations = null;
    /** @var Hashed */
    protected $hashed = null;
    /** @var Uploader\Processor */
    protected $processor = null;

    /**
     * @throws Exceptions\UploadException
     */
    public function __construct()
    {
        $lang = new Uploader\Translations();
        $this->infoStorage = $this->getInfoStorage($lang);
        $this->dataStorage = $this->getDataStorage($lang);
        $format = InfoFormat\AFormat::getFormat($lang, $this->getFormat());
        $this->targetSearch = $this->getTarget($lang);
        $this->calculations = $this->getCalc();
        $this->hashed = $this->getHashed();
        $this->key = Keys\AKey::getVariant($lang, $this->targetSearch, $this->getKeyVariant());
        $driver = new Uploader\DriveFile($lang, $this->infoStorage, $format, $this->key);
        $this->processor = $this->getProcessor($lang, $driver, $this->dataStorage, $this->hashed);
    }

    protected function getFormat(): int
    {
        return InfoFormat\AFormat::FORMAT_JSON;
    }

    protected function getKeyVariant(): int
    {
        return Keys\AKey::VARIANT_VOLUME;
    }

    protected function getInfoStorage(Translations $lang): InfoStorage\AStorage
    {
        return new InfoStorage\Volume($lang);
    }

    protected function getDataStorage(Translations $lang): DataStorage\AStorage
    {
        return new DataStorage\VolumeBasic($lang);
    }

    protected function getTarget(Translations $lang): DataStorage\TargetSearch
    {
        return new DataStorage\TargetSearch($lang);
    }

    protected function getCalc(): Calculates
    {
        return new Calculates(262144);
    }

    protected function getHashed(): Hashed
    {
        return new Hashed();
    }

    protected function getProcessor(Translations $lang, Uploader\DriveFile $driver, DataStorage\AStorage $storage, Hashed $hashed): Uploader\Processor
    {
        return new Uploader\Processor($lang, $driver, $storage, $hashed);
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
            return Response\DoneResponse::initError($sharedKey, InfoFormat\Data::init(), $ex);
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
            return Response\UploadResponse::initError($sharedKey, InfoFormat\Data::init(), $e);
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
            return Response\TruncateResponse::initError($sharedKey, InfoFormat\Data::init(), $e);
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
        $partsCounter = $this->calculations->calcParts($length);
        try {
            $this->targetSearch->setTargetDir($targetPath)->setRemoteFileName($remoteFileName)->process();
            $this->key->generateKeys();
            $dataPack = InfoFormat\Data::init()->setData(
                $this->targetSearch->getFinalTargetName(),
                $this->targetSearch->getTemporaryTargetLocation(),
                $length,
                $partsCounter,
                $this->calculations->getBytesPerPart()
            );
            return Response\InitResponse::initOk($this->key->getSharedKey(), $this->processor->init($dataPack, $this->key->getSharedKey()));

        } catch (Exceptions\UploadException $e) { // obecne neco spatne
            return Response\InitResponse::initError(InfoFormat\Data::init()->setData(
                $remoteFileName, '', $length, $partsCounter, $this->calculations->getBytesPerPart()
            ), $e);
        }
    }
}
