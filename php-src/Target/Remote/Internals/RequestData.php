<?php

namespace kalanis\UploadPerPartes\Target\Remote\Internals;


/**
 * Class RequestData
 * @package kalanis\UploadPerPartes\Target\Remote\Internals
 * Configuration of remote querying on internal
 */
class RequestData
{
    public string $path = '';
    /** @var array<string, array<string, string|int>> */
    public array $context = [];
}
