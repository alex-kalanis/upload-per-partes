<?php

namespace kalanis\UploadPerPartes\Target\Remote\Internals;


use kalanis\UploadPerPartes\UploadException;


/**
 * Class Client
 * @package kalanis\UploadPerPartes\Target\Remote\Internals
 * Remote query itself
 * @link https://www.php.net/manual/en/reserved.variables.httpresponseheader.php
 */
class Client
{
    /**
     * @param RequestData $data
     * @throws UploadException
     * @return ResponseData
     * @link https://wiki.php.net/rfc/http-last-response-headers
     */
    public function request(RequestData $data): ResponseData
    {
        $response = @file_get_contents($data->path, false, stream_context_create($data->context));
        if (false === $response) {
            throw new UploadException('Bad request content', 503);
        }
        if (
            function_exists('http_get_last_response_headers')
            && function_exists('http_clear_last_response_headers')
        ) { // 8.4+
            // @codeCoverageIgnoreStart
            $http_response_header = http_get_last_response_headers();
            http_clear_last_response_headers();
        }
        if (false !== strpos($data->path, '://')) {
            if (empty($http_response_header)) {
                // @codeCoverageIgnoreStart
                throw new UploadException('Bad response headers', 503);
            }
            // @codeCoverageIgnoreEnd
        } else {
            $http_response_header = [
                'HTTP/0.0 999',
                'local-file',
                'path:'.$data->path
            ];
        }
        // @codeCoverageIgnoreEnd
        return new ResponseData(
            empty($http_response_header) ? [] : $this->parseHeaders($http_response_header)
            , $response);
    }

    /**
     * @param array<string> $headers
     * @return array{
     *     response_code?: int,
     *     last-modified?: string,
     *     accept-ranges?: string,
     *     cache-control?: string,
     *     expires?: string,
     *     content-length?: int,
     *     content-type?: string,
     *     server?: string
     * }|array<string|int, string|int>
     */
    private function parseHeaders(array $headers): array
    {
        $head = [];
        foreach ($headers as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $head[trim($t[0])] = trim($t[1]);
            } else {
                $head[] = $v;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                    $head['response_code'] = intval($out[1]);
                }
            }
        }
        return $head;
    }
}
