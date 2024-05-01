<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders;


use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class TempName
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders
 */
class TempName extends AEncoder
{
    public function toPath(Data $data): string
    {
        return $data->tempName;
    }
}
