<?php

namespace kalanis\UploadPerPartes\Target\Remote;


use kalanis\UploadPerPartes\Interfaces\IOperations;
use kalanis\UploadPerPartes\Responses\BasicResponse;
use kalanis\UploadPerPartes\UploadException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;


/**
 * Class Psr
 * @package kalanis\UploadPerPartes\Target\Remote
 * Process responses from PSR
 */
class Psr implements IOperations
{
    protected ClientInterface $client;
    protected Psr\Request $request;
    protected Psr\Response $response;

    public function __construct(ClientInterface $client, Psr\Request $request, Psr\Response $response)
    {
        $this->client = $client;
        $this->request = $request;
        $this->response = $response;
    }

    public function init(string $targetPath, string $fileName, int $length, string $clientData = ''): BasicResponse
    {
        try {
            return $this->response->init(
                $this->client->sendRequest(
                    $this->request->init($targetPath, $fileName, $length)
                ),
                $clientData
            );
        } catch (ClientExceptionInterface $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function check(string $serverData, int $segment, string $method, string $clientData = ''): BasicResponse
    {
        try {
            return $this->response->check(
                $this->client->sendRequest(
                    $this->request->check($serverData, $segment, $method)
                ),
                $clientData
            );
        } catch (ClientExceptionInterface $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function truncate(string $serverData, int $segment, string $clientData = ''): BasicResponse
    {
        try {
            return $this->response->truncate(
                $this->client->sendRequest(
                    $this->request->truncate($serverData, $segment)
                ),
                $clientData
            );
        } catch (ClientExceptionInterface $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function upload(string $serverData, string $content, string $method, string $clientData = ''): BasicResponse
    {
        try {
            return $this->response->upload(
                $this->client->sendRequest(
                    $this->request->upload($serverData, $content, $method)
                ),
                $clientData
            );
        } catch (ClientExceptionInterface $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function done(string $serverData, string $clientData = ''): BasicResponse
    {
        try {
            return $this->response->done(
                $this->client->sendRequest(
                    $this->request->done($serverData)
                ),
                $clientData
            );
        } catch (ClientExceptionInterface $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function cancel(string $serverData, string $clientData = ''): BasicResponse
    {
        try {
            return $this->response->cancel(
                $this->client->sendRequest(
                    $this->request->cancel($serverData)
                ),
                $clientData
            );
        } catch (ClientExceptionInterface $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
