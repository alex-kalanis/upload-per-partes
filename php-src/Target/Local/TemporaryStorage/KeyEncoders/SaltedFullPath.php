<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders;


use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\Uploader\RandomStrings;


/**
 * Class SaltedFull
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage\KeyEncoders
 */
class SaltedFullPath extends AEncoder
{
    public function toPath(Data $data): string
    {
        $want = $data->targetDir . $data->targetName;
        $halfLen = intval(ceil(strlen($want) / 2));
        return RandomStrings::generate($halfLen) . $want . RandomStrings::generate($halfLen);
    }
}
