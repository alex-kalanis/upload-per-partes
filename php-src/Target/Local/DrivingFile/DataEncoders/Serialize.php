<?php

namespace kalanis\UploadPerPartes\Target\Local\DrivingFile\DataEncoders;


use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class Serialize
 * @package kalanis\UploadPerPartes\ServerData\DataModifiers
 * Driver file - format into serialized string
 */
class Serialize extends AEncoder
{
    public function unpack(string $content): Data
    {
        $data = @unserialize($content, [
            'allowed_classes' => [Data::class],
            'max_depth' => 2,
        ]);
        if (false === $data) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        if (!$data instanceof Data) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        return $data->clear();
    }

    public function pack(Data $data): string
    {
        return strval(serialize($data));
    }
}
