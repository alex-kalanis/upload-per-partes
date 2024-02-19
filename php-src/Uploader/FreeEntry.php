<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\GenerateKeys\AKey;
use kalanis\UploadPerPartes\InfoFormat;


/**
 * Class FreeEntry
 * @package kalanis\UploadPerPartes\ServerData
 * Process metadata from server and client to get info about upload
 */
class FreeEntry
{
    /** @var TargetSearch */
    protected $targetSearch = null;
    /** @var Calculates */
    protected $calculations = null;
    /** @var AKey */
    protected $generate = null;

    public function __construct(TargetSearch $targetSearch, Calculates $calculations, AKey $generate)
    {
        $this->targetSearch = $targetSearch;
        $this->calculations = $calculations;
        $this->generate = $generate;
    }

    /**
     * @param string $targetPath
     * @param string $remoteFileName
     * @param int<0, max> $length
     * @throws UploadException
     * @return InfoFormat\Data
     */
    public function find(string $targetPath, string $remoteFileName, int $length): InfoFormat\Data
    {
        $this->targetSearch->setTargetDir($targetPath)->setRemoteFileName($remoteFileName)->process();
        return InfoFormat\Data::init()->setData(
            $this->targetSearch->getFinalTargetName(),
            $this->targetSearch->getTemporaryTargetLocation(),
            $length,
            $this->calculations->calcParts($length),
            $this->calculations->getBytesPerPart(),
            0,
            $targetPath,
            $this->generate->generateKey($this->targetSearch)
        );
    }
}
