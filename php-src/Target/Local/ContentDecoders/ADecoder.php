<?php

namespace kalanis\UploadPerPartes\Target\Local\ContentDecoders;


use kalanis\UploadPerPartes\Interfaces\IContentDecoder;
use kalanis\UploadPerPartes\Traits\TLangInit;


/**
 * Class ADecoder
 * @package kalanis\UploadPerPartes\Target\Local\ContentDecoders
 * Decode content from client sent packed in something
 */
abstract class ADecoder implements IContentDecoder
{
    use TLangInit;
}
