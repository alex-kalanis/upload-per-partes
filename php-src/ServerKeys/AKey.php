<?php

namespace kalanis\UploadPerPartes\ServerKeys;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces\IDriverLocation;


/**
 * Class AKey
 * @package kalanis\UploadPerPartes\ServerKeys
 * Connect shared key and local details
 */
abstract class AKey
{
    /**
     * @param IDriverLocation $data
     * @throws UploadException
     * @return string
     */
    abstract public function fromData(IDriverLocation $data): string;
}
