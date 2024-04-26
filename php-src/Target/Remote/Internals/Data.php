<?php

namespace kalanis\UploadPerPartes\Target\Remote\Internals;


/**
 * Class Data
 * @package kalanis\UploadPerPartes\Target\Remote\Internals
 * Configuration of remote querying on internal
 */
class Data
{
    public string $path = '';
    /** @var array<string, array<string, string|int>> */
    public array $context = [];
}
