<?php

namespace kalanis\UploadPerPartes\Target\Local\ContentDecoders;


use kalanis\UploadPerPartes\UploadException;


/**
 * Class Hex
 * @package kalanis\UploadPerPartes\Target\Local\ContentDecoders
 * Decode content from client sent packed in hexadecimal numbers instead of raw content
 */
class Hex extends ADecoder
{
    public function getMethod(): string
    {
        return 'hex';
    }

    public function decode(string $data): string
    {
        $pack = @hex2bin($data);
        if (false === $pack) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        return $pack;
    }
}
