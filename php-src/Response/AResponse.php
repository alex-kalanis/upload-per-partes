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

    /** @var string */
    protected $sharedKey = '';
    /** @var string */
    protected $errorMessage = self::STATUS_OK;
    /** @var string */
    protected $status = self::STATUS_OK;

    final public function __construct()
    {
    }
}
