<?php

namespace kalanis\UploadPerPartes\Target\Remote\Psr;


use JsonException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\UploadException;
use Psr\Http\Message\ResponseInterface;
use stdClass;


/**
 * Class Response
 * @package kalanis\UploadPerPartes\Target\Remote\Psr
 * Process responses from PSR
 */
class Response
{
    use TLang;

    protected Responses\Factory $responseFactory;

    public function __construct(Responses\Factory $responseFactory, ?Interfaces\IUppTranslations $lang = null)
    {
        $this->responseFactory = $responseFactory;
        $this->setUppLang($lang);
    }

    /**
     * @param ResponseInterface $response
     * @param string $clientData
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function init(ResponseInterface $response, string $clientData): Responses\BasicResponse
    {
        $parsed = $this->parseResponse($response);
        if (Responses\BasicResponse::STATUS_OK == $parsed->status) {
            $data = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_INIT);
            /** @var Responses\InitResponse $data */
            return $data->setPassedInitData(
                strval($parsed->name ?? null),
                intval(max(0, $parsed->totalParts ?? 0)),
                intval(max(0,$parsed->lastKnownPart ?? 0)),
                intval(max(0, $parsed->partSize ?? 0)),
                strval($parsed->encoder ?? 'base64'),
                strval($parsed->check ?? 'md5')
            )->setBasics(
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        } else {
            return $this->responseError(
                strval($parsed->message ?? ''),
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        }
    }

    /**
     * @param ResponseInterface $response
     * @param string $clientData
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function check(ResponseInterface $response, string $clientData): Responses\BasicResponse
    {
        $parsed = $this->parseResponse($response);
        if (Responses\BasicResponse::STATUS_OK == $parsed->status) {
            $data = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_CHECK);
            /** @var Responses\CheckResponse $data */
            return $data->setChecksum(
                strval($parsed->method ?? ''),
                strval($parsed->checksum ?? '')
            )->setBasics(
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        } else {
            return $this->responseError(
                strval($parsed->message ?? ''),
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        }
    }

    /**
     * @param ResponseInterface $response
     * @param string $clientData
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function truncate(ResponseInterface $response, string $clientData): Responses\BasicResponse
    {
        $parsed = $this->parseResponse($response);
        if (Responses\BasicResponse::STATUS_OK == $parsed->status) {
            $data = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_TRUNCATE);
            /** @var Responses\LastKnownResponse $data */
            return $data->setLastKnown(
                intval(max(0, $parsed->lastKnown ?? 0))
            )->setBasics(
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        } else {
            return $this->responseError(
                strval($parsed->message ?? ''),
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        }
    }

    /**
     * @param ResponseInterface $response
     * @param string $clientData
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function upload(ResponseInterface $response, string $clientData): Responses\BasicResponse
    {
        $parsed = $this->parseResponse($response);
        if (Responses\BasicResponse::STATUS_OK == $parsed->status) {
            $data = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_UPLOAD);
            /** @var Responses\LastKnownResponse $data */
            return $data->setLastKnown(
                intval(max(0, $parsed->lastKnown ?? 0))
            )->setBasics(
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        } else {
            return $this->responseError(
                strval($parsed->message ?? ''),
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        }
    }

    /**
     * @param ResponseInterface $response
     * @param string $clientData
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function done(ResponseInterface $response, string $clientData): Responses\BasicResponse
    {
        $parsed = $this->parseResponse($response);
        if (Responses\BasicResponse::STATUS_OK == $parsed->status) {
            $data = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_DONE);
            /** @var Responses\DoneResponse $data */
            return $data->setFinalName(
                strval($parsed->name ?? '')
            )->setBasics(
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        } else {
            return $this->responseError(
                strval($parsed->message ?? ''),
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        }
    }

    /**
     * @param ResponseInterface $response
     * @param string $clientData
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    public function cancel(ResponseInterface $response, string $clientData): Responses\BasicResponse
    {
        $parsed = $this->parseResponse($response);
        if (Responses\BasicResponse::STATUS_OK == $parsed->status) {
            $data = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_CANCEL);
            /** @var Responses\BasicResponse $data */
            return $data->setBasics(
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        } else {
            return $this->responseError(
                strval($parsed->message ?? ''),
                strval($parsed->serverKey ?? ''),
                $clientData
            );
        }
    }

    /**
     * @param string $message
     * @param string $serverKey
     * @param string $clientData
     * @throws UploadException
     * @return Responses\BasicResponse
     */
    protected function responseError(string $message, string $serverKey, string $clientData): Responses\BasicResponse
    {
        $data = $this->responseFactory->getResponse(Responses\Factory::RESPONSE_ERROR);
        /** @var Responses\ErrorResponse $data */
        return $data->setErrorMessage($message)->setBasics($serverKey, $clientData);
    }

    /**
     * @param ResponseInterface $response
     * @throws UploadException
     * @return stdClass
     */
    protected function parseResponse(ResponseInterface $response): stdClass
    {
        try {
            $body = $response->getBody();
            $body->rewind();
            $parsed = json_decode($body->getContents(), false, 2, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
        if (!$parsed instanceof stdClass) {
            // @codeCoverageIgnoreStart
            // this one is on phpstan
            throw new UploadException($this->getUppLang()->uppBadResponse(''));
        }
        // @codeCoverageIgnoreEnd
        return $parsed;
    }
}
