<?php

namespace kalanis\UploadPerPartes\Target\Remote\Internals;


class ResponseData
{
    /** @var array<string|int, string|int> */
    public array $headers = [];
    public ?string $data = null;

    /**
     * @param array{
     *      response_code?: int,
     *      last-modified?: string,
     *      accept-ranges?: string,
     *      cache-control?: string,
     *      expires?: string,
     *      content-length?: int,
     *      content-type?: string,
     *      server?: string
     * }|array<string|int, string|int> $headers
     * @param string|null $data
     */
    public function __construct(
        array $headers = [],
        ?string $data = null
    )
    {
        $this->headers = $headers;
        $this->data = $data;
    }
}
