<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class AEncoder
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders
 * Storing data in temporary storage during upload itself - modify what from name will be used
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
