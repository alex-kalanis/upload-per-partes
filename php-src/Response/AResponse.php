<?php

namespace UploadPerPartes\Response;

/**
 * Class AResponse
 * @package UploadPerPartes\Response
 * Responses for client
 */
abstract class AResponse implements \JsonSerializable
{
    const STATUS_OK = 'OK';
    const STATUS_FAIL = 'FAIL';
    const STATUS_COMPLETE = 'COMPLETE';
    const STATUS_BEGIN = 'BEGIN';
    const STATUS_CONTINUE = 'CONTINUE';
    const STATUS_FAILED_CONTINUE = 'FAILED_CONTINUE';

    protected $sharedKey = '';
    protected $errorMessage = self::STATUS_OK;
    protected $status = self::STATUS_OK;
}