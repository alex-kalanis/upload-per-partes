<?php

namespace kalanis\UploadPerPartes;


use kalanis\UploadPerPartes\Interfaces;
use Psr\Container\ContainerInterface;


/**
 * Class Uploader
 * @package kalanis\UploadPerPartes
 * Main server library for drive upload per-partes
 */
class Uploader
{
    protected Interfaces\IOperations $target;
    protected Responses\ErrorResponse $errorResponse;

    /**
     * @param ContainerInterface|null $container
     * @param array{
     *              "calc_size"?: int|object|null,
     *              "temp_location"?: string|null,
     *              "target_location"?: string|null,
     *              "lang"?: string|int|object|null,
     *              "target"?: string|int|object|null,
     *              "data_encoder"?: string|int|object|null,
     *              "data_modifier"?: string|int|object|null,
     *              "key_encoder"?: string|int|object|null,
     *              "key_modifier"?: string|int|object|null,
     *              "driving_file"?: string|int|object|null,
     *              "temp_storage"?: string|int|object|null,
     *              "temp_encoder"?: string|int|object|null,
     *              "final_storage"?: string|int|object|null,
     *              "final_encoder"?: string|int|object|null,
     *              "checksum"?: string|null,
     *              "decoder"?: string|null,
     *              "can_continue"?: bool|null,
     *             } $params
     * @throws UploadException
     */
    public function __construct(
        ?ContainerInterface $container = null,
        $params = []
    )
    {
        $this->errorResponse = new Responses\ErrorResponse();
        $this->target = (new Target\Factory(new Uploader\LangFactory(), $container))->getTarget(new Uploader\Config($params));
    }

    /**
     * Upload file by parts, create driving file
     * @param string $targetPath
     * @param string $remoteFileName posted file name
     * @param int<0, max> $length complete file size
     * @param string $clientData stored string from client
     * @return Responses\BasicResponse
     */
    public function init(string $targetPath, string $remoteFileName, int $length, string $clientData = '̈́'): Responses\BasicResponse
    {
        try {
            return $this->target->init($targetPath, $remoteFileName, $length, $clientData);
        } catch (UploadException $ex) {
            return $this->errorResponse->setError($ex)->setBasics('', $clientData);
        }
    }

    /**
     * Check already uploaded parts
     * @param string $serverData
     * @param int<0, max> $segment
     * @param string $method which method will be used on segment
     * @param string $clientData stored string from client
     * @return Responses\BasicResponse
     */
    public function check(string $serverData, int $segment, string $method, string $clientData = ''): Responses\BasicResponse
    {
        try {
            return $this->target->check($serverData, $segment, $method, $clientData);
        } catch (UploadException $ex) {
            return $this->errorResponse->setError($ex)->setBasics($serverData, $clientData);
        }
    }

    /**
     * Delete problematic segments
     * @param string $serverData stored string for server
     * @param int<0, max> $segment
     * @param string $clientData stored string from client
     * @return Responses\BasicResponse
     */
    public function truncateFrom(string $serverData, int $segment, string $clientData = ''): Responses\BasicResponse
    {
        try {
            return $this->target->truncate($serverData, $segment, $clientData);
        } catch (UploadException $ex) {
            return $this->errorResponse->setError($ex)->setBasics($serverData, $clientData);
        }
    }

    /**
     * Upload file by parts, use driving file
     * @param string $serverData stored string for server
     * @param string $content binary content
     * @param string $method how is content encoded
     * @param string $clientData stored string from client
     * @return Responses\BasicResponse
     */
    public function upload(string $serverData, string $content, string $method, string $clientData = ''): Responses\BasicResponse
    {
        try {
            return $this->target->upload($serverData, $content, $method, $clientData);
        } catch (UploadException $ex) {
            return $this->errorResponse->setError($ex)->setBasics($serverData, $clientData);
        }
    }

    /**
     * Upload file by parts, final status
     * @param string $serverData stored string for server
     * @param string $clientData stored string from client
     * @return Responses\BasicResponse
     */
    public function done(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        try {
            return $this->target->done($serverData, $clientData);
        } catch (UploadException $ex) {
            return $this->errorResponse->setError($ex)->setBasics($serverData, $clientData);
        }
    }

    /**
     * Upload file by parts, final status
     * @param string $serverData stored string for server
     * @param string $clientData stored string from client
     * @return Responses\BasicResponse
     */
    public function cancel(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        try {
            return $this->target->cancel($serverData, $clientData);
        } catch (UploadException $ex) {
            return $this->errorResponse->setError($ex)->setBasics($serverData, $clientData);
        }
    }
}
