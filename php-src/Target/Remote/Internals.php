<?php

namespace kalanis\UploadPerPartes\Target\Remote;


use kalanis\UploadPerPartes\Interfaces\IOperations;
use kalanis\UploadPerPartes\Responses\BasicResponse;


/**
 * Class Internals
 * @package kalanis\UploadPerPartes\Target\Remote
 * Process responses from internal php functions
 */
class Internals implements IOperations
{
    protected Internals\Client $client;
    protected Internals\Request $request;
    protected Internals\Response $response;

    public function __construct(Internals\Client $client, Internals\Request $request, Internals\Response $response)
    {
        $this->client = $client;
        $this->request = $request;
        $this->response = $response;
    }

    public function init(string $targetPath, string $fileName, int $length, string $clientData = ''): BasicResponse
    {
        return $this->response->init(
            $this->client->request(
                $this->request->init($targetPath, $fileName, $length)
            ),
            $clientData
        );
    }

    public function check(string $serverData, int $segment, string $method, string $clientData = ''): BasicResponse
    {
        return $this->response->check(
            $this->client->request(
                $this->request->check($serverData, $segment, $method)
            ),
            $clientData
        );
    }

    public function truncate(string $serverData, int $segment, string $clientData = ''): BasicResponse
    {
        return $this->response->truncate(
            $this->client->request(
                $this->request->truncate($serverData, $segment)
            ),
            $clientData
        );
    }

    public function upload(string $serverData, string $content, string $method, string $clientData = ''): BasicResponse
    {
        return $this->response->upload(
            $this->client->request(
                $this->request->upload($serverData, $content, $method)
            ),
            $clientData
        );
    }

    public function done(string $serverData, string $clientData = ''): BasicResponse
    {
        return $this->response->done(
            $this->client->request(
                $this->request->done($serverData)
            ),
            $clientData
        );
    }

    public function cancel(string $serverData, string $clientData = ''): BasicResponse
    {
        return $this->response->cancel(
            $this->client->request(
                $this->request->cancel($serverData)
            ),
            $clientData
        );
    }
}
