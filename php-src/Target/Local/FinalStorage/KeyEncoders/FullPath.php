<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders;


use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class FullPath
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders
 */
class FullPath extends AEncoder
{
    public function toPath(Data $data): string
    {
        return $data->targetDir . $data->targetName;
    }
}
