<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\UploadException;
use kalanis\UploadPerPartes\Responses\BasicResponse;


/**
 * Interface IOperations
 * @package kalanis\UploadPerPartes\Interfaces
 * Which operations are available for set storage
 */
interface IOperations
{
    /**
     * @param string $targetPath
     * @param string $targetFileName
     * @param int $length
     * @param string $clientData
     * @throws UploadException
     * @return BasicResponse
     */
    public function init(string $targetPath, string $targetFileName, int $length, string $clientData = '̈́'): BasicResponse;

    /**
     * @param string $serverData
     * @param int $segment
     * @param string $method
     * @param string $clientData
     * @throws UploadException
     * @return BasicResponse
     */
    public function check(string $serverData, int $segment, string $method, string $clientData = ''): BasicResponse;

    /**
     * @param string $serverData
     * @param int $segment
     * @param string $clientData
     * @throws UploadException
     * @return BasicResponse
     */
    public function truncate(string $serverData, int $segment, string $clientData = ''): BasicResponse;

    /**
     * @param string $serverData
     * @param string $content
     * @param string $method
     * @param string $clientData
     * @throws UploadException
     * @return BasicResponse
     */
    public function upload(string $serverData, string $content, string $method, string $clientData = ''): BasicResponse;

    /**
     * @param string $serverData
     * @param string $clientData
     * @throws UploadException
     * @return BasicResponse
     */
    public function done(string $serverData, string $clientData = ''): BasicResponse;

    /**
     * @param string $serverData
     * @param string $clientData
     * @throws UploadException
     * @return BasicResponse
     */
    public function cancel(string $serverData, string $clientData = ''): BasicResponse;
}
