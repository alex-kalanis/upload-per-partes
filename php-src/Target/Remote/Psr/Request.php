<?php

namespace kalanis\UploadPerPartes\Target\Remote\Psr;


use kalanis\UploadPerPartes\Target\Remote;
use Psr\Http\Message\RequestInterface;


/**
 * Class Request
 * @package kalanis\UploadPerPartes\Target\Remote\Psr
 * Create requests for PSR
 */
class Request
{
    protected RequestInterface $requestObject;
    protected Remote\Config $config;

    public function __construct(RequestInterface $requestObject, Remote\Config $config)
    {
        $this->requestObject = $requestObject;
        $this->config = $config;
    }

    public function init(string $targetPath, string $fileName, int $fileSize): RequestInterface
    {
        return $this->processQuery($this->config->initPath, compact('targetPath', 'fileName', 'fileSize'));
    }

    public function check(string $serverData, int $segment, string $method): RequestInterface
    {
        return $this->processQuery($this->config->checkPath, compact('serverData', 'segment', 'method'));
    }

    public function truncate(string $serverData, int $segment): RequestInterface
    {
        return $this->processQuery($this->config->truncatePath, compact('serverData', 'segment'));
    }

    public function upload(string $serverData, string $content, string $method): RequestInterface
    {
        return $this->processQuery($this->config->uploadPath, compact('serverData', 'content', 'method'));
    }

    public function done(string $serverData): RequestInterface
    {
        return $this->processQuery($this->config->donePath, compact('serverData'));
    }

    public function cancel(string $serverData): RequestInterface
    {
        return $this->processQuery($this->config->cancelPath, compact('serverData'));
    }

    /**
     * @param string $path
     * @param array<string, string|int> $params
     * @return RequestInterface
     */
    protected function processQuery(string $path, array $params): RequestInterface
    {
        $request = clone $this->requestObject;
        // need to create POST with these (and other) params
        $uri = clone $request->getUri()
            ->withHost($this->config->targetHost)
            ->withPort($this->config->targetPort)
            ->withPath($this->config->pathPrefix . $path)
            ->withQuery('')
        ;
        $body = $request->getBody();
        $body->rewind();
        $body->write(http_build_query($params));
        $request = $request
            ->withUri($uri)
            ->withBody($body)
            ->withAddedHeader('Content-type', 'application/x-www-form-urlencoded');
        ;
        return $request;
    }
}
