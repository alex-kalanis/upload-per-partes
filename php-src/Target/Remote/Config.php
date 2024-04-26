<?php

namespace kalanis\UploadPerPartes\Target\Remote;


/**
 * Class Config
 * @package kalanis\UploadPerPartes\Target\Remote
 * Configuration of remote querying
 */
class Config
{
    public string $targetHost = 'http://localhost';
    public ?int $targetPort = 80;
    public string $pathPrefix = '/upload/v1';

    public string $initPath = '/init';
    public string $checkPath = '/check';
    public string $truncatePath = '/truncate';
    public string $uploadPath = '/upload';
    public string $donePath = '/done';
    public string $cancelPath = '/cancel';
}
