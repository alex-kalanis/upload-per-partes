<?php

namespace kalanis\UploadPerPartes;


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
    /** @var InfoStorage\AStorage */
    protected $infoStorage = null;
    /** @var DataStorage\AStorage */
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
        $format = InfoFormat\Factory::getFormat($lang, $this->getFormat());
        $this->targetSearch = $this->getTarget($lang, $this->infoStorage, $this->dataStorage);
        $this->calculations = $this->getCalc();
        $this->hashed = $this->getHashed();
        $this->key = Keys\Factory::getVariant($lang, $this->targetSearch, $this->getKeyVariant());
        $this->driver = new Uploader\DriveFile($lang, $this->infoStorage, $format, $this->key);
        $this->processor = $this->getProcessor($lang, $this->driver, $this->dataStorage, $this->hashed);
    }

    protected function getFormat(): int
    {
        return InfoFormat\Factory::FORMAT_JSON;
    }

    protected function getKeyVariant(): int
    {
        return Keys\Factory::VARIANT_VOLUME;
    }

    protected function getTranslations(): Interfaces\IUPPTranslations
    {
        return new Uploader\Translations();
    }

    protected function getInfoStorage(Interfaces\IUPPTranslations $lang): InfoStorage\AStorage
    {
        return new InfoStorage\Volume($lang);
    }

    protected function getDataStorage(Interfaces\IUPPTranslations $lang): DataStorage\AStorage
    {
        return new DataStorage\VolumeBasic($lang);
    }

    protected function getTarget(Interfaces\IUPPTranslations $lang, InfoStorage\AStorage $infoStorage, DataStorage\AStorage $dataStorage): Uploader\TargetSearch
    {
        return new Uploader\TargetSearch($lang, $infoStorage, $dataStorage);
    }

    protected function getCalc(): Calculates
    {
        return new Calculates(262144);
    }

    protected function getHashed(): Hashed
    {
        return new Hashed();
    }

    protected function getProcessor(Interfaces\IUPPTranslations $lang, Uploader\DriveFile $driver, DataStorage\AStorage $storage, Hashed $hashed): Uploader\Processor
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
