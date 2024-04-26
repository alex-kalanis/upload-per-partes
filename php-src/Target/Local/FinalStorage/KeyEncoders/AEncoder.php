<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class AEncoder
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders
 * Storing data in final storage - modify what from name will be used
 */
abstract class AEncoder
{
    use TLangInit;

    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    abstract public function toPath(Data $data): string;
}
