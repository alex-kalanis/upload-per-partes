<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders;


use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\Uploader\RandomStrings;


/**
 * Class SaltedName
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders
 */
class SaltedName extends AEncoder
{
    public function toPath(Data $data): string
    {
        $want = $data->targetName;
        $halfLen = intval(ceil(strlen($want) / 2));
        return RandomStrings::generate($halfLen) . $want . RandomStrings::generate($halfLen);
    }
}
