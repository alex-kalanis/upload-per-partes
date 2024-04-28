<?php

namespace kalanis\UploadPerPartes\Responses;


use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\UploadException;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Responses
 * Responses from server to client
 */
class Factory
{
    use TLangInit;

    public const RESPONSE_INIT = 'init';
    public const RESPONSE_CHECK = 'check';
    public const RESPONSE_TRUNCATE = 'truncate';
    public const RESPONSE_UPLOAD = 'upload';
    public const RESPONSE_DONE = 'done';
    public const RESPONSE_CANCEL = 'cancel';
    public const RESPONSE_ERROR = 'error';

    /** @var array<string, class-string<BasicResponse>> */
    protected array $responses = [
        self::RESPONSE_INIT => InitResponse::class,
        self::RESPONSE_CHECK => CheckResponse::class,
        self::RESPONSE_TRUNCATE => LastKnownResponse::class,
        self::RESPONSE_UPLOAD => LastKnownResponse::class,
        self::RESPONSE_DONE => DoneResponse::class,
        self::RESPONSE_CANCEL => BasicResponse::class,
        self::RESPONSE_ERROR => ErrorResponse::class,
    ];

    /**
     * @param string $type
     * @throws UploadException
     * @return BasicResponse
     */
    public function getResponse(string $type): BasicResponse
    {
        try {
            if (isset($this->responses[$type])) {
                $reflection = new ReflectionClass($this->responses[$type]);
                $class = $reflection->newInstance();
                if ($class instanceof BasicResponse) {
                    return $class;
                }
            }
            throw new UploadException($this->getUppLang()->uppBadResponse($type));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
