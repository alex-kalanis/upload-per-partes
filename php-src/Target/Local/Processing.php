<?php

namespace kalanis\UploadPerPartes\Target\Local;


use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\Uploader;


/**
 * Class Processing
 * @package kalanis\UploadPerPartes\Target\Local
 * Main library for processing upload per-partes
 */
class Processing implements Interfaces\IOperations
{
    use TLang;

    /** @var Uploader\Config */
    protected Uploader\Config $uploadConfig;
    /** @var Uploader\DataPack */
    protected Uploader\DataPack $dataPack;
    /** @var Uploader\Calculates */
    protected Uploader\Calculates $calculates;
    /** @var DrivingFile\DrivingFile */
    protected DrivingFile\DrivingFile $drivingFile;
    /** @var TemporaryStorage\TemporaryStorage */
    protected TemporaryStorage\TemporaryStorage $tempStorage;
    /** @var FinalStorage\FinalStorage */
    protected FinalStorage\FinalStorage $finalStorage;
    /** @var Responses\Factory */
    protected Responses\Factory $responseFactory;
    /** @var Checksums\Factory */
    protected Checksums\Factory $checksumFactory;
    /** @var ContentDecoders\Factory */
    protected ContentDecoders\Factory $decoderFactory;

    /**
     * @param Uploader\Config $config
     * @param Interfaces\IUppTranslations|null $lang
     * @throws UploadException
     */
    public function __construct(
        Uploader\Config $config,
        ?Interfaces\IUppTranslations $lang = null
    )
    {
        $this->uploadConfig = $config;
        $this->dataPack = new Uploader\DataPack(new Uploader\Data());
        $this->calculates = new Uploader\Calculates($config);
        $this->drivingFile = (new DrivingFile\Factory($lang))->getDrivingFile($config);
        $this->tempStorage = (new TemporaryStorage\Factory($lang))->getTemporaryStorage($config);
        $this->finalStorage = (new FinalStorage\Factory($lang))->getFinalStorage($config);
        $this->responseFactory = new Responses\Factory($lang);
        $this->checksumFactory = new Checksums\Factory($lang);
        $this->decoderFactory = new ContentDecoders\Factory($lang);
    }

    /**
     * Upload file by parts, create driving file
     * @param string $targetPath
     * @param string $targetFileName posted file name
     * @param int<0, max> $length complete file size
     * @param string $clientData stored string from client
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function init(string $targetPath, string $targetFileName, int $length, string $clientData = '̈́'): Responses\BasicResponse
    {
        if (empty($targetFileName)) {
            throw new UploadException($this->getUppLang()->uppSentNameIsEmpty());
        }
        $initialData = $this->dataPack->fillSizes(
            $this->dataPack->create(
                $targetPath,
                $targetFileName,
                $length
            ),
            $this->calculates->calcParts($length),
            $this->calculates->getBytesPerPart(),
            0
        );
        $data = $this->tempStorage->fillData($this->dataPack->fillTempData(
            $initialData,
            $this->uploadConfig
        ));

        $alreadyKnown = false;
        if ($this->drivingFile->existsByData($data)) {
            $currentKey = $this->drivingFile->keyByData($data);
            if (!$this->uploadConfig->canContinue) {
                throw new UploadException($this->getUppLang()->uppDriveFileAlreadyExists($currentKey));
            }
            $data = $this->drivingFile->get($currentKey);
            $alreadyKnown = true;
        }

        if ($this->tempStorage->exists($data) && !$alreadyKnown) {
            $this->tempStorage->remove($data);
        }

        $response = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_INIT);
        /** @var Responses\InitResponse $response */
        return $response
            ->setInitData(
                $data,
                $this->uploadConfig->decoder ? strval($this->uploadConfig->decoder) : 'base64',
                $this->uploadConfig->checksum ? strval($this->uploadConfig->checksum) : 'md5'
            )
            ->setBasics(
                $this->drivingFile->storeByData($data),
                $clientData
            )
        ;
    }

    /**
     * Check already uploaded parts
     * @param string $serverData
     * @param int<0, max> $segment
     * @param string $method
     * @param string $clientData stored string from client
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function check(string $serverData, int $segment, string $method, string $clientData = ''): Responses\BasicResponse
    {
        $data = $this->drivingFile->get($serverData);
        $checksumClass = $this->checksumFactory->getChecksum($method);
        $response = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_CHECK);
        /** @var Responses\CheckResponse $response */
        return $response
            ->setChecksum(
                $checksumClass->getMethod(),
                $checksumClass->checksum(
                    $this->tempStorage->checksumData(
                        $data,
                        $this->calculates->bytesFromSegment($data, $segment)
                    )
                )
            )
            ->setBasics($this->drivingFile->storeByData($data), $clientData)
        ;
    }

    /**
     * Delete problematic segments
     * @param string $serverData stored string for server
     * @param int<0, max> $segment
     * @param string $clientData stored string from client
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function truncate(string $serverData, int $segment, string $clientData = ''): Responses\BasicResponse
    {
        $data = $this->drivingFile->get($serverData);
        if ($data->lastKnownPart < $segment) {
            throw new UploadException($this->getUppLang()->uppSegmentOutOfBounds($segment));
        }
        if (!$this->tempStorage->truncate($data, $this->calculates->bytesFromSegment($data, $segment))) {
            throw new UploadException($this->getUppLang()->uppCannotTruncateFile($data->targetName));
        }
        $response = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_TRUNCATE);
        /** @var Responses\LastKnownResponse $response */
        return $response
            ->setLastKnown($segment)
            ->setBasics($this->drivingFile->storeByData($this->dataPack->lastKnown($data, $segment)), $clientData)
        ;
    }

    /**
     * Upload file by parts, use driving file
     * @param string $serverData stored string for server
     * @param string $content binary content
     * @param string $method how the content is encoded
     * @param string $clientData stored string from client
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function upload(string $serverData, string $content, string $method, string $clientData = ''): Responses\BasicResponse
    {
        $data = $this->drivingFile->get($serverData);
        if (!$this->tempStorage->upload($data, $this->decoderFactory->getDecoder($method)->decode($content))) {
            throw new UploadException($this->getUppLang()->uppCannotWriteFile($data->targetName));
        }
        $segment = $this->dataPack->nextSegment($data);
        $response = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_TRUNCATE);
        /** @var Responses\LastKnownResponse $response */
        return $response
            ->setLastKnown($segment)
            ->setBasics($this->drivingFile->storeByData($this->dataPack->lastKnown($data, $segment)), $clientData)
        ;
    }

    /**
     * Upload file by parts, final status
     * @param string $serverData stored string for server
     * @param string $clientData stored string from client
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function done(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        $data = $this->drivingFile->get($serverData);
        $key = $this->finalStorage->findName($data);
        if (!$this->finalStorage->store($key, $this->tempStorage->read($data))) {
            throw new UploadException($this->getUppLang()->uppCannotWriteFile($data->targetName));
        }
        if (!$this->tempStorage->remove($data)) {
            throw new UploadException($this->getUppLang()->uppCannotRemoveData($data->targetName));
        }
        if (!$this->drivingFile->removeByData($data)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($data->targetName));
        }
        $response = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_DONE);
        /** @var Responses\DoneResponse $response */
        return $response
            ->setFinalName($key)
            ->setBasics($serverData, $clientData)
        ;
    }

    /**
     * Upload file by parts, cancel process
     * @param string $serverData stored string for server
     * @param string $clientData stored string from client
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function cancel(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        $data = $this->drivingFile->get($serverData);
        if (!$this->tempStorage->remove($data)) {
            throw new UploadException($this->getUppLang()->uppCannotRemoveData($data->targetName));
        }
        if (!$this->drivingFile->removeByData($data)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRemove($data->targetName));
        }
        return $this->responseFactory
            ->getResponse(Responses\Factory::RESPONSE_CANCEL)
            ->setBasics($serverData, $clientData)
        ;
    }
}
