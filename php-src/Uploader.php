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
    /** @var Interfaces\IUPPTranslations */
    protected $lang = null;
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
    /** @var Uploader\DriveFile must stay for tests */
    protected $driver = null;
    /** @var Uploader\Processor */
    protected $processor = null;

    /**
     * @throws Exceptions\UploadException
     */
    public function __construct()
    {
        $this->lang = $this->getTranslations();
        $this->infoStorage = $this->getInfoStorage();
        $this->dataStorage = $this->getDataStorage();
        $this->targetSearch = $this->getTarget($this->infoStorage, $this->dataStorage);
        $this->calculations = $this->getCalc();
        $this->hashed = $this->getHashed();
        $this->key = $this->getKeyFactory()->getVariant($this->targetSearch, $this->getKeyVariant());
        $this->driver = new Uploader\DriveFile($this->infoStorage, $this->getInfoFormatFactory()->getFormat($this->getInfoFormat()), $this->key, $this->lang);
        $this->processor = $this->getProcessor($this->driver, $this->dataStorage, $this->hashed);
    }

    protected function getTranslations(): Interfaces\IUPPTranslations
    {
        return new Uploader\Translations();
    }

    protected function getInfoStorage(): Interfaces\IInfoStorage
    {
        return new InfoStorage\Volume($this->lang);
    }

    protected function getDataStorage(): Interfaces\IDataStorage
    {
        return new DataStorage\VolumeBasic($this->lang);
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

    protected function getInfoFormatFactory(): InfoFormat\Factory
    {
        return new InfoFormat\Factory($this->lang);
    }

    protected function getInfoFormat(): int
    {
        return InfoFormat\Factory::FORMAT_JSON;
    }

    protected function getKeyFactory(): Keys\Factory
    {
        return new Keys\Factory($this->lang);
    }

    protected function getKeyVariant(): int
    {
        return Keys\Factory::VARIANT_VOLUME;
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
            return Response\CancelResponse::initCancel($this->lang, $sharedKey);
        } catch (Exceptions\UploadException $ex) {
            return Response\CancelResponse::initError($this->lang, $sharedKey, $ex);
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
            return Response\DoneResponse::initDone($this->lang, $sharedKey, $this->processor->done($sharedKey));
        } catch (Exceptions\UploadException $ex) {
            return Response\DoneResponse::initError($this->lang, $sharedKey, InfoFormat\Data::init(), $ex);
        }
    }

    /**
     * Upload file by parts, use driving file
     * @param string $sharedKey
     * @param string $content binary content
     * @param int<0, max>|null $segment where it save
     * @return Response\AResponse
     */
    public function upload(string $sharedKey, string $content, ?int $segment = null): Response\AResponse
    {
        try {
            return Response\UploadResponse::initOK($this->lang, $sharedKey, $this->processor->upload($sharedKey, $content, $segment));
        } catch (Exceptions\UploadException $e) {
            return Response\UploadResponse::initError($this->lang, $sharedKey, InfoFormat\Data::init(), $e);
        }
    }

    /**
     * Delete problematic segments
     * @param string $sharedKey
     * @param int<0, max> $segment
     * @return Response\AResponse
     */
    public function truncateFrom(string $sharedKey, int $segment): Response\AResponse
    {
        try {
            return Response\TruncateResponse::initOK($this->lang, $sharedKey, $this->processor->truncateFrom($sharedKey, $segment));
        } catch (Exceptions\UploadException $e) {
            return Response\TruncateResponse::initError($this->lang, $sharedKey, InfoFormat\Data::init(), $e);
        }
    }

    /**
     * Check already uploaded parts
     * @param string $sharedKey
     * @param int<0, max> $segment
     * @return Response\AResponse
     */
    public function check(string $sharedKey, int $segment): Response\AResponse
    {
        try {
            return Response\CheckResponse::initOK($this->lang, $sharedKey, $this->processor->check($sharedKey, $segment));
        } catch (Exceptions\UploadException $e) {
            return Response\CheckResponse::initError($this->lang, $sharedKey, $e);
        }
    }

    /**
     * Upload file by parts, create driving file
     * @param string $targetPath
     * @param string $remoteFileName posted file name
     * @param int<0, max> $length complete file size
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
            return Response\InitResponse::initOk($this->lang, $this->key->getSharedKey(), $this->processor->init($dataPack, $this->key->getSharedKey()));

        } catch (Exceptions\UploadException $e) { // obecne neco spatne
            return Response\InitResponse::initError($this->lang, InfoFormat\Data::init()->setData(
                $remoteFileName, '', $length, $partsCounter, $this->calculations->getBytesPerPart()
            ), $e);
        }
    }
}
