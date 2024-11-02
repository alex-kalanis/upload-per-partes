<?php

namespace kalanis\UploadPerPartes\Target\Remote\Internals;


use kalanis\UploadPerPartes\Target\Remote;


/**
 * Class Request
 * @package kalanis\UploadPerPartes\Target\Remote\Internals
 * Create requests for internal functions
 */
class Request
{
    protected Remote\Config $config;
    protected RequestData $data;

    public function __construct(Remote\Config $config, RequestData $data)
    {
        $this->config = $config;
        $this->data = $data;
    }

    public function init(string $targetPath, string $fileName, int $fileSize): RequestData
    {
        return $this->processQuery($this->config->initPath, compact('targetPath', 'fileName', 'fileSize'));
    }

    public function check(string $serverData, int $segment, string $method): RequestData
    {
        return $this->processQuery($this->config->checkPath, compact('serverData', 'segment', 'method'));
    }

    public function truncate(string $serverData, int $segment): RequestData
    {
        return $this->processQuery($this->config->truncatePath, compact('serverData', 'segment'));
    }

    public function upload(string $serverData, string $content, string $method): RequestData
    {
        return $this->processQuery($this->config->uploadPath, compact('serverData', 'content', 'method'));
    }

    public function done(string $serverData): RequestData
    {
        return $this->processQuery($this->config->donePath, compact('serverData'));
    }

    public function cancel(string $serverData): RequestData
    {
        return $this->processQuery($this->config->cancelPath, compact('serverData'));
    }

    /**
     * @param string $path
     * @param array<string, string|int> $params
     * @return RequestData
     */
    protected function processQuery(string $path, array $params): RequestData
    {
        $data = clone $this->data;
        $remotePath = $this->config->targetHost;
        if (!empty($this->config->targetPort) && (80 != $this->config->targetPort)) {
            $remotePath .= ':' . $this->config->targetPort;
        }
        $remotePath .= $this->config->pathPrefix . $path;

        $data->path = $remotePath;
        // ultra-idiotic variant with only building a query
        $content = http_build_query($params);
        $header = [
            'Content-type: application/x-www-form-urlencoded',
            'Content-length: ' . mb_strlen($content),
        ];
        $data->context = [
            'ssl' => [
//                'verify_peer' => false,
//                'verify_peer_name' => false,
//                'allow_self_signed' => true,
            ],
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $header),
                'timeout' => 30,
                'content' => $content,
            ]
        ];

        return $data;
    }
}
