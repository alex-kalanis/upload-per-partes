<?php

namespace kalanis\UploadPerPartes\Response;


use JsonSerializable;


/**
 * Class AResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses for client
 */
abstract class AResponse implements JsonSerializable
{
    const STATUS_OK = 'OK';
    const STATUS_FAIL = 'FAIL';

    protected $sharedKey = '';
    protected $errorMessage = self::STATUS_OK;
    protected $status = self::STATUS_OK;
}
