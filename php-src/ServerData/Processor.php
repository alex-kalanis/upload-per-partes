<?php

namespace kalanis\UploadPerPartes\ServerData;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class Processor
 * @package kalanis\UploadPerPartes\ServerData
 * Process metadata from server and client to get info about upload
 */
class Processor
{
    use TLang;

    /** @var string what path/prefix on storage to driver file */
    protected $pathPrefix = '';
    /** @var string real driver file name on storage */
    protected $sharedKey = '';

    public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }

    public function packData(InfoFormat\Data $local): Data
    {
        $data = new Data();
        $data->pathPrefix = $local->targetPath;
        $data->sharedKey = $local->driverName;
        return $data;
    }

    public function composePack(Data $local): string
    {
        return base64_encode(strval(json_encode($local)));
    }

    /**
     * @param string $serverData
     * @throws UploadException
     * @return Data
     */
    public function readPack(string $serverData): Data
    {
        $base = base64_decode($serverData, true);
        if (false === $base) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        $arrived = json_decode($base);
        if (is_null($arrived)) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        $remote = new Data();
        $remote->pathPrefix = $arrived->pathPrefix;
        $remote->sharedKey = $arrived->sharedKey;
        return $remote;
    }
}
