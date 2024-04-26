<?php

namespace kalanis\UploadPerPartes\Target\Remote\Internals;


use kalanis\UploadPerPartes\UploadException;


/**
 * Class Client
 * @package kalanis\UploadPerPartes\Target\Remote\Internals
 * Remote query itself
 */
class Client
{
    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    public function request(Data $data): string
    {
        $response = @file_get_contents($data->path, false, stream_context_create($data->context));
        if (false === $response) {
            throw new UploadException('Bad request content', 503);
        }
        return $response;
    }
}
