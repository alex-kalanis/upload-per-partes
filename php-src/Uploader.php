<?php

namespace kalanis\UploadPerPartes;


use kalanis\UploadPerPartes\Interfaces;


/**
 * Class Uploader
 * @package kalanis\UploadPerPartes
 * Main server library for drive upload per-partes
 */
class Uploader
{
    /** @var Interfaces\IUPPTranslations */
    protected $lang = null;
    /** @var Interfaces\IDataStorage */
    protected $dataStorage = null;
    /** @var Uploader\FreeEntry */
    protected $findFreeEntry = null;
    /** @var Uploader\DriveFile must stay for tests */
    protected $driver = null;
    /** @var ServerData\Processor */
    protected $serverData = null;
    /** @var Uploader\Processor */
    protected $processor = null;

    /**
     * @throws Exceptions\UploadException
     */
    public function __construct()
    {
        $this->lang = $this->getTranslations();
        $infoStorage = $this->getInfoStorage();
        $this->dataStorage = $this->getDataStorage();
        $this->findFreeEntry = $this->getFreeEntry(
            $this->getTarget($infoStorage, $this->dataStorage),
            $this->getCalc(),
            $this->getGenerateKeyFactory()->getVariant($this->getGenerateKeyVariant())
        );
        $infoFormat = $this->getInfoFormatFactory()->getFormat($this->getInfoFormat());
        $this->driver = new Uploader\DriveFile(
            $infoStorage,
            $infoFormat,
            $this->getServerKeyFactory()->getVariant($this->getServerKeyVariant()),
            $this->lang);
        $this->serverData = $this->getServerData($this->lang);
        $this->processor = $this->getProcessor(
            $this->driver,
            $this->dataStorage,
            $this->getHashed()
        );
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

    protected function getCalc(): Uploader\Calculates
    {
        return new Uploader\Calculates(262144);
    }

    protected function getFreeEntry(Uploader\TargetSearch $targetSearch, Uploader\Calculates $calc, GenerateKeys\AKey $generate): Uploader\FreeEntry
    {
        return new Uploader\FreeEntry($targetSearch, $calc, $generate);
    }

    protected function getHashed(): Uploader\Hashed
    {
        return new Uploader\Hashed();
    }

    protected function getServerData(Interfaces\IUPPTranslations $lang): ServerData\Processor
    {
        return new ServerData\Processor($lang);
    }

    protected function getInfoFormatFactory(): InfoFormat\Factory
    {
        return new InfoFormat\Factory($this->lang);
    }

    protected function getInfoFormat(): int
    {
        return InfoFormat\Factory::FORMAT_JSON;
    }

    protected function getServerKeyFactory(): ServerKeys\Factory
    {
        return new ServerKeys\Factory($this->lang);
    }

    protected function getServerKeyVariant(): int
    {
        return ServerKeys\Factory::VARIANT_VOLUME;
    }

    protected function getGenerateKeyFactory(): GenerateKeys\Factory
    {
        return new GenerateKeys\Factory($this->lang);
    }

    protected function getGenerateKeyVariant(): int
    {
        return GenerateKeys\Factory::VARIANT_CLEAR;
    }

    protected function getProcessor(
        Uploader\DriveFile $driver,
        Interfaces\IDataStorage $storage,
        Uploader\Hashed $hashed,
        ?Interfaces\IUPPTranslations $lang = null
    ): Uploader\Processor
    {
        return new Uploader\Processor($driver, $storage, $hashed, $lang);
    }

    /**
     * Upload file by parts, final status
     * @param string $serverData stored string for server
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function cancel(string $serverData, string $clientData = ''): Response\AResponse
    {
        try {
            $upPath = $this->serverData->readPack($serverData);
            $this->processor->cancel($upPath);
            return Response\CancelResponse::initCancel($this->lang, $serverData, $clientData);
        } catch (Exceptions\UploadException $ex) {
            return Response\CancelResponse::initError($this->lang, $serverData, $ex, $clientData);
        }
    }

    /**
     * Upload file by parts, final status
     * @param string $serverData stored string for server
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function done(string $serverData, string $clientData = ''): Response\AResponse
    {
        try {
            return Response\DoneResponse::initDone(
                $this->lang,
                $serverData,
                $this->processor->done($this->serverData->readPack($serverData)),
                $clientData
            );
        } catch (Exceptions\UploadException $ex) {
            return Response\DoneResponse::initError($this->lang, $serverData, InfoFormat\Data::init(), $ex, $clientData);
        }
    }

    /**
     * Upload file by parts, use driving file
     * @param string $serverData stored string for server
     * @param string $content binary content
     * @param int<0, max>|null $segment where it save
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function upload(string $serverData, string $content, ?int $segment = null, string $clientData = ''): Response\AResponse
    {
        try {
            return Response\UploadResponse::initOK(
                $this->lang,
                $serverData,
                $this->processor->upload($this->serverData->readPack($serverData), $content, $segment),
                $clientData
            );
        } catch (Exceptions\UploadException $e) {
            return Response\UploadResponse::initError($this->lang, $serverData, InfoFormat\Data::init(), $e, $clientData);
        }
    }

    /**
     * Delete problematic segments
     * @param string $serverData stored string for server
     * @param int<0, max> $segment
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function truncateFrom(string $serverData, int $segment, string $clientData = ''): Response\AResponse
    {
        try {
            return Response\TruncateResponse::initOK(
                $this->lang,
                $serverData,
                $this->processor->truncateFrom($this->serverData->readPack($serverData), $segment),
                $clientData
            );
        } catch (Exceptions\UploadException $e) {
            return Response\TruncateResponse::initError($this->lang, $serverData, InfoFormat\Data::init(), $e, $clientData);
        }
    }

    /**
     * Check already uploaded parts
     * @param string $serverData
     * @param int<0, max> $segment
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function check(string $serverData, int $segment, string $clientData = ''): Response\AResponse
    {
        try {
            return Response\CheckResponse::initOK(
                $this->lang,
                $serverData,
                $this->processor->check($this->serverData->readPack($serverData), $segment),
                $clientData
            );
        } catch (Exceptions\UploadException $e) {
            return Response\CheckResponse::initError($this->lang, $serverData, $e, $clientData);
        }
    }

    /**
     * Upload file by parts, create driving file
     * @param string $targetPath
     * @param string $remoteFileName posted file name
     * @param int<0, max> $length complete file size
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function init(string $targetPath, string $remoteFileName, int $length, string $clientData  = '̈́'): Response\AResponse
    {
        try {
            $dataPack = $this->findFreeEntry->find($targetPath, $remoteFileName, $length);
            return Response\InitResponse::initOk(
                $this->lang,
                $this->serverData->composePack($this->serverData->packData($dataPack)),
                $this->processor->init($dataPack),
                $clientData
            );
        } catch (Exceptions\UploadException $e) { // something go wrong
            return Response\InitResponse::initError(
                $this->lang,
                InfoFormat\Data::init()->setData(
                    $remoteFileName, '', $length, 0, 0
                ),
                $e,
                $clientData
            );
        }
    }
}
