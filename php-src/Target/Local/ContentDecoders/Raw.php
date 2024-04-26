<?php

namespace kalanis\UploadPerPartes\Target\Local\ContentDecoders;


/**
 * Class Raw
 * @package kalanis\UploadPerPartes\Target\Local\ContentDecoders
 * Decode content from client which is not packed
 */
class Raw extends ADecoder
{
    public function getMethod(): string
    {
        return 'raw';
    }

    public function decode(string $data): string
    {
        return $data;
    }
}
