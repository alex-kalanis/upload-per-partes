<?php

namespace kalanis\UploadPerPartes;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Uploader\Calculates;
use kalanis\UploadPerPartes\Uploader\Hashed;


/**
 * Class Uploader
 * @package kalanis\UploadPerPartes
 * Main server library for drive upload per-partes
 */
class Uploader
{
    /** @var Keys\AKey */
    protected $key = null;
    /** @var Interfaces\IInfoStorage */
    protected $infoStorage = null;
    /** @var Interfaces\IDataStorage */
    protected $dataStorage = null;
    /** @var Uploader\TargetSearch */
    protected $targetSearch = null;
    /** @var Calculates */
    protected $calculations = null;
    /** @var Hashed */
    protected $hashed = null;
    /** @var Uploader\DriveFile */
    protected $driver = null;
    /** @var Uploader\Processor */
    protected $processor = null;

    /**
     * @throws Exceptions\UploadException
     */
    public function __construct()
    {
        $lang = $this->getTranslations();
        $this->infoStorage = $this->getInfoStorage($lang);
        $this->dataStorage = $this->getDataStorage($lang);
        $format = InfoFormat\Factory::getFormat($this->getFormat(), $lang);
        $this->targetSearch = $this->getTarget($this->infoStorage, $this->dataStorage, $lang);
        $this->calculations = $this->getCalc();
        $this->hashed = $this->getHashed();
        $this->key = Keys\Factory::getVariant($this->targetSearch, $this->getKeyVariant(), $lang);
        $this->driver = new Uploader\DriveFile($this->infoStorage, $format, $this->key, $lang);
        $this->processor = $this->getProcessor($this->driver, $this->dataStorage, $this->hashed, $lang);
    }

    protected function getFormat(): int
    {
        return InfoFormat\Factory::FORMAT_JSON;
    }

    protected function getKeyVariant(): int
    {
        return Keys\Factory::VARIANT_VOLUME;
    }

    protected function getTranslations(): ?Interfaces\IUPPTranslations
    {
        return new Uploader\Translations();
    }

    protected function getInfoStorage(?Interfaces\IUPPTranslations $lang = null): Interfaces\IInfoStorage
    {
        return new InfoStorage\Volume($lang);
    }

    protected function getDataStorage(?Interfaces\IUPPTranslations $lang = null): Interfaces\IDataStorage
    {
        return new DataStorage\VolumeBasic($lang);
    }

    protected function getTarget(
        Interfaces\IInfoStorage $infoStorage,
        Interfaces\IDataStorage $dataStorage,
        Interfaces\IUPPTranslations $lang = null
    ): Uploader\TargetSearch
    {
        return new Uploader\TargetSearch($infoStorage, $dataStorage, $lang);
    }

    protected function getCalc(): Calculates
    {
        return new Calculates(262144);
    }

    protected function getHashed(): Hashed
    {
        return new Hashed();
    }

    protected function getProcessor(
        Uploader\DriveFile $driver,
        Interfaces\IDataStorage $storage,
        Hashed $hashed,
        ?Interfaces\IUPPTranslations $lang = null
    ): Uploader\Processor
    {
        return new Uploader\Processor($driver, $storage, $hashed, $lang);
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
