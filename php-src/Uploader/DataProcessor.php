<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class DataProcessor
 * @package kalanis\UploadPerPartes
 * Processing upload per-partes
 */
class DataProcessor
{
    use TLang;

    /** @var Interfaces\IDataStorage */
    protected $storage = null;
    /** @var CheckByHash */
    protected $checkHash = null;

    public function __construct(
        Interfaces\IDataStorage $storage,
        CheckByHash $checkByHash,
        ?Interfaces\IUPPTranslations $lang = null
    )
    {
        $this->setUppLang($lang);
        $this->storage = $storage;
        $this->checkHash = $checkByHash;
    }

    /**
     * Upload file by parts, final status - cancel that
     * @param ServerData\Data $data
     * @throws Exceptions\UploadException
     * @return bool
     */
    public function cancel(ServerData\Data $data): bool
    {
        return $this->storage->remove($data->tempDir . $data->tempName);
    }

    /**
     * Upload file by parts, use driving file
     * @param ServerData\Data $data
     * @param string $content binary content
     * @param int<0, max>|null $segment where it save
     * @throws Exceptions\UploadException
     * @return ServerData\Data
     */
    public function upload(ServerData\Data $data, string $content, ?int $segment = null): ServerData\Data
    {
        if (!is_numeric($segment)) {
            $segment = $data->lastKnownPart + 1;
            $this->storage->addPart($data->tempDir . $data->tempName, $content);
            $data = $this->updateLastPart($data, $segment);
        } else {
            if ($segment > $data->lastKnownPart + 1) {
                throw new Exceptions\UploadException($this->getUppLang()->uppReadTooEarly($data->remoteName));
            }
            $this->storage->addPart($data->tempDir . $data->tempName, $content, $segment * $data->bytesPerPart);
        }

        return $data;
    }

    /**
     * Delete problematic segments
     * @param ServerData\Data $data
     * @param int<0, max> $segment
     * @throws Exceptions\UploadException
     * @return ServerData\Data
     */
    public function truncateFrom(ServerData\Data $data, int $segment): ServerData\Data
    {
        $this->checkSegment($data, $segment);
        $this->storage->truncate($data->tempDir . $data->tempName, $data->bytesPerPart * $segment);
        return $this->updateLastPart($data, $segment, false);
    }

    /**
     * @param ServerData\Data $data
     * @param int<0, max> $last
     * @param bool $checkContinuous
     * @throws Exceptions\UploadException
     * @return ServerData\Data
     */
    protected function updateLastPart(ServerData\Data $data, int $last, bool $checkContinuous = true): ServerData\Data
    {
        if ($checkContinuous) {
            if (($data->lastKnownPart + 1) != $last) {
                throw new Exceptions\UploadException($this->getUppLang()->uppDriveFileNotContinuous($data->remoteName));
            }
        }
        $data->lastKnownPart = $last;
        return $data;
    }

    /**
     * Check already uploaded parts
     * @param ServerData\Data $data
     * @param int<0, max> $segment
     * @throws Exceptions\UploadException
     * @return string
     */
    public function check(ServerData\Data $data, int $segment): string
    {
        $this->checkSegment($data, $segment);
        return $this->checkHash->calcHash($this->storage->getPart($data->tempDir . $data->tempName, $data->bytesPerPart * $segment, $data->bytesPerPart));
    }

    /**
     * @param ServerData\Data $data
     * @param int $segment
     * @throws Exceptions\UploadException
     */
    protected function checkSegment(ServerData\Data $data, int $segment): void
    {
        if (0 > $segment) {
            throw new Exceptions\UploadException($this->getUppLang()->uppSegmentOutOfBounds($segment));
        }
        if ($segment > $data->partsCount) {
            throw new Exceptions\UploadException($this->getUppLang()->uppSegmentOutOfBounds($segment));
        }
        if ($segment > $data->lastKnownPart) {
            throw new Exceptions\UploadException($this->getUppLang()->uppSegmentNotUploadedYet($segment));
        }
    }
}
