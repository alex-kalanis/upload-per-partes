<?php

namespace kalanis\UploadPerPartes\Target\Local\ContentDecoders;


use kalanis\UploadPerPartes\UploadException;


/**
 * Class Base64
 * @package kalanis\UploadPerPartes\Target\Local\ContentDecoders
 * Decode content from client sent packed in base64
 */
class Base64 extends ADecoder
{
    public function getMethod(): string
    {
        return 'base64';
    }

    public function decode(string $data): string
    {
        $pack = @base64_decode($data, true);
        if (false === $pack) {
            throw new UploadException($this->getUppLang()->uppIncomingDataCannotDecode());
        }
        return $pack;
    }
}
